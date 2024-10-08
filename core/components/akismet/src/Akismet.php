<?php

namespace modmore\Akismet;

use DateInterval;
use DateTime;
use modX;
use xPDOException;
use modmore\Akismet\Exceptions\InvalidAPIKeyException;
use GuzzleHttp\Client;

class Akismet {

    /** @var modX $modx */
    public $modx;

    /** @var array $values */
    private $values;

    /** @var array|null $hookConfig */
    private $hookConfig;

    /** @var string $apiKey */
    private $apiKey;

    /**
     * @throws xPDOException
     */
    public function __construct(modX $modx)
    {
        $this->modx = $modx;
        $this->apiKey = $this->modx->getOption('akismet.api_key', '');
        $this->modx->lexicon->load('akismet:default');
        // Load xPDO package
        if (!$this->modx->addPackage('akismet', dirname(__DIR__) . '/model/')) {
            throw new xPDOException('Unable to load Akismet xPDO package!');
        }
    }

    /**
     * Matches form values with specified field keys then returns
     * @return array
     */
    private function getFields(): array
    {
        $fields = [];
        foreach ($this->hookConfig as $key => $param) {
            if (substr($key, 0, 7) === 'akismet') {
                // Check for any commas, and if so combine fields
                if (strpos($param, ',') !== false) {
                    $pieces = explode(',', $param);
                    $pieces = array_map('trim', $pieces);
                    $param = '';
                    foreach ($pieces as $k => $piece) {
                        $param .= $this->values[$piece];

                        // Only add a space if it's not the last iteration.
                        end($pieces);
                        if ($k !== key($pieces)) {
                            $param .= ' ';
                        }
                    }
                }

                // Either grab the submitted value for the provided param, or return the param itself to read the config
                $fields[$key] = $this->values[$param] ?? $param;
            }
        }

        // If a honeypot field is in use, add the field name
        if (!empty($this->hookConfig['akismetHoneypotField'])) {
            $fields['honeypot_field_name'] = $this->hookConfig['akismetHoneypotField'];
        }

        return $fields;
    }

    /**
     * @param array $values An array of submitted values.
     * @param array|null $hookConfig Configuration from a hook/calling snippet.
     *      When provided an array, will be used to read field names (&akismetAuthor=`author_field_name`, etc).
     *      When null, $values should have the akismet field names (comment_author, comment_content, etc)
     * @return bool True if spam, false if not.
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws InvalidAPIKeyException
     */
    public function checkSpam(array $values, $hookConfig = null): bool
    {
        if (!$this->apiKey) {
            throw new InvalidAPIKeyException('Missing API key...');
        }

        $this->values = $values;
        $this->hookConfig = $hookConfig;

        $params = $this->getParams();

        $client = new Client();
        $akismetCheck = $client->post("https://{$this->apiKey}.rest.akismet.com/1.1/comment-check", [
            'form_params' => $params,
            'http_errors' => false
        ]);
        $spamCheck = (string)$akismetCheck->getBody()->getContents();

        if ($this->modx->getOption('akismet.debug')) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Akismet verdict "' . $spamCheck . " for parameters: " . print_r($params, true));
        }

        $isSpam = $spamCheck === 'true';

        $form = $this->modx->newObject(\AkismetForm::class, $params);
        $form->set('reported_status', $isSpam ? 'spam' : 'notspam');
        if (!$form->save()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unable to save Akismet spam check data: ' . print_r($params, true));
        }

        $this->cleanup();
        $this->countSpam($isSpam);

        return $isSpam;
    }

    /**
     * Retrieves parameters to be used for both the API request and the database record.
     * @return array
     */
    private function getParams(): array
    {
        $permalink = $this->modx->makeUrl(
            $this->modx->resource->get('id'),
            $this->modx->resource->get('context_key'),
            '', 'full');

        $params = [
            'blog' => $this->modx->getOption('site_url'),
            'blog_lang' => $this->modx->getOption('cultureKey'),
            'user_ip' => $this->getIpAddress(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'referrer' => $_SERVER['HTTP_REFERER'],
            'permalink' => $permalink,
            'blog_charset' => $this->modx->getOption('modx_charset'),
            'comment_date_gmt' => gmdate("Y-m-d H:i:s", time()),
            'comment_modified_gmt' => NULL,
        ];

        // If a $hookConfig array is present, we assume a hook is being used.
        if ($this->hookConfig) {

            $fields = $this->getFields();

            $params = array_merge($params, [
                'comment_type' => $fields['akismetType'] ?? '',
                'comment_author' => $fields['akismetAuthor'] ?? '',
                'comment_author_email' => $fields['akismetAuthorEmail'] ?? '',
                'comment_author_url' => $fields['akismetAuthorUrl'] ?? '',
                'comment_content' => $fields['akismetContent'] ?? '',
                'recheck_reason' => $fields['akismetRecheckReason'] ?? '',
                'user_role' => $fields['akismetUserRole'] ?? '',
                'is_test' => $fields['akismetTest'] ?? '',
            ]);

            // If a honeypot field is in use, include the name and value
            if (!empty($fields['akismetHoneypotField'])) {
                $params['honeypot_field_name'] = $fields['honeypot_field_name'];
                $params['honeypot_field_value'] = $this->values[$fields['akismetHoneypotField']] ?? '';
                $params[$fields['honeypot_field_name']] = $params['honeypot_field_value'];
            }
        }

        // If there is no $hookConfig specified then we assume a custom script is being used, and use values directly.
        else {
            $params = array_merge($params, [
                'comment_type' => $this->values['comment_type'] ?? '',
                'comment_author' => $this->values['comment_author'] ?? '',
                'comment_author_email' => $this->values['comment_author_email'] ?? '',
                'comment_author_url' => $this->values['comment_author_url'] ?? '',
                'comment_content' => $this->values['comment_content'] ?? '',
                'recheck_reason' => $this->values['recheck_reason'] ?? '',
                'user_role' => $this->values['user_role'] ?? '',
                'is_test' => $this->values['is_test'] ?? '',
            ]);

            if ($this->values['honeypot_field_name']) {
                $params['honeypot_field_name'] = $this->values['honeypot_field_name'] ?? '';
                $params['honeypot_field_value'] = $this->values['honeypot_field_value'] ?? '';
                $params[$this->values['honeypot_field_name']] = $this->values['honeypot_field_value'] ?? '';
            }
        }

        return $params;
    }

    /**
     * @param array $params
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws InvalidAPIKeyException
     */
    public function submitSpam(array $params): bool
    {
        if (!$this->apiKey) {
            throw new InvalidAPIKeyException('Missing API key...');
        }

        if ($params['honeypot_field_name']) {
            $params[$params['honeypot_field_name']] = $params['honeypot_field_value'];
            unset($params['honeypot_field_value']);
        }

        $client = new Client();
        $akismetCheck = $client->post("https://{$this->apiKey}.rest.akismet.com/1.1/submit-spam", [
            'form_params' => $params,
            'http_errors' => false
        ]);
        if ($akismetCheck->getHeader('X-akismet-alert-code')) {
            return false;
        }
        else {
            return true;
        }
    }

    /**
     * @param array $params
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws InvalidAPIKeyException
     */
    public function submitHam(array $params): bool
    {
        if (!$this->apiKey) {
            throw new InvalidAPIKeyException('Missing API key...');
        }

        if ($params['honeypot_field_name']) {
            $params[$params['honeypot_field_name']] = $params['honeypot_field_value'];
            unset($params['honeypot_field_value']);
        }

        $client = new Client();
        $akismetCheck = $client->post("https://{$this->apiKey}.rest.akismet.com/1.1/submit-ham", [
            'form_params' => $params,
            'http_errors' => false
        ]);
        if ($akismetCheck->getHeader('X-akismet-alert-code')) {
            return false;
        }
        else {
            return true;
        }
    }

    private function getIpAddress()
    {
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ips = array_map('trim', $ips);
            $ip = $ips[0];
        }
        else {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }

        $ip = filter_var($ip, FILTER_VALIDATE_IP);

        return $ip === false ? '::1' : $ip;
    }

    /**
     * Timestamps a file and attempts to clean up old records every 24 hours.
     */
    private function cleanup()
    {
        $path = $this->modx->getOption('akismet.core_path') . '.cleanup';
        if (!file_exists($path)) {
            file_put_contents($path, time());
            return;
        }

        // If the last cleanup was over 24 hours ago, run again.
        $lastRun = (int) file_get_contents($path);
        if (!empty($lastRun) and $lastRun < (time() - 86400)) {
            $this->removeOldRecords();
            file_put_contents($path, time());
        }
    }

    /**
     * Removes any records older than the number of days set in the system setting.
     */
    private function removeOldRecords()
    {
        $days = (int) trim($this->modx->getOption('akismet.cleanup_days_old', null, '',true));
        if ($days > 0) {
            $now = new DateTime();
            $then = $now->sub(DateInterval::createFromDateString("{$days} days"));
            $deleteBefore = $then->format('Y-m-d H:i:s');

            if ($deleteBefore) {
                $count = $this->modx->removeCollection(\AkismetForm::class, [
                    'created_at:<' => $deleteBefore
                ]);
                $this->modx->log(modX::LOG_LEVEL_INFO, '[Akismet] Cleaned up ' . $count
                    . ' spam analysis records from before ' . $deleteBefore);
            }
        }
    }

    private function countSpam(bool $isSpam)
    {
        $settingKey = $isSpam ? 'akismet.total_spam' : 'akismet.total_ham';
        $setting = $this->modx->getObject('modSystemSetting', [ 'key' => $settingKey]);
        if ($setting) {
            $setting->set('value', $setting->get('value') + 1);
            $setting->save();
        }
        else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, '[Akismet] Could not find setting ' . $settingKey . ' to keep track of total number of spam/ham');
        }
    }
}
