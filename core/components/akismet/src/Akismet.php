<?php

namespace modmore\Akismet;

use modX;
use xPDOException;
use fiHooks;
use LoginHooks;
use modmore\Akismet\Exceptions\InvalidAPIKeyException;
use GuzzleHttp\Client;

class Akismet {

    /** @var modX $modx */
    public $modx;

    /** @var fiHooks|LoginHooks $hook */
    private $hook;

    /** @var string $apiKey */
    private $apiKey;

    /**
     * @throws InvalidAPIKeyException|xPDOException
     */
    public function __construct(modX $modx)
    {
        $this->modx = $modx;
        $this->apiKey = $this->_loadAPIKey();
        $this->modx->lexicon->load('akismet:default');
        // Load xPDO package
        if (!$this->modx->addPackage('akismet', dirname(__DIR__) . '/model/')) {
            throw new xPDOException('Unable to load Akismet xPDO package!');
        }
    }

    /**
     * @throws InvalidAPIKeyException
     */
    private function _loadAPIKey(): string
    {
        $apiKey = $this->modx->getOption('akismet.api_key');
        if (!$apiKey) {
            throw new InvalidAPIKeyException('Invalid API Key.');
        }
        return $apiKey;
    }

    /**
     * Matches form values with specified field keys then returns
     * @return array
     */
    private function getFields(): array
    {
        $config = [];
        if ($this->hook instanceof LoginHooks) {
            $config = $this->hook->login->controller->config;
        }
        else if ($this->hook instanceof fiHooks) {
            $config = $this->hook->config;
        }

        $values = $this->hook->getValues();

        $fields = [];
        foreach ($config as $key => $param) {
            if (substr($key, 0, 7) === 'akismet') {
                // Either grab the submitted value for the provided param, or return the param itself to read the config
                $fields[$key] = $values[$param] ?? $param;
            }
        }
        return $fields;
    }

    /**
     * @param fiHooks|LoginHooks $hook
     * @return bool True if spam, false if not.
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkSpam($hook): bool
    {
        $this->hook = $hook;

        $fields = $this->getFields();

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
            'comment_type' => $fields['akismetType'] ?? '',
            'comment_author' => $fields['akismetAuthor'] ?? '',
            'comment_author_email' => $fields['akismetAuthorEmail'] ?? '',
            'comment_author_url' => $fields['akismetAuthorUrl'] ?? '',
            'comment_content' => $fields['akismetContent'] ?? '',
            'blog_charset' => $this->modx->getOption('modx_charset'),
            'recheck_reason' => $fields['akismetRecheckReason'] ?? '',
            'user_role' => $fields['akismetUserRole'] ?? '',
            'is_test' => $fields['akismetTest'] ?? '',
            'comment_date_gmt' => gmdate("Y-m-d H:i:s", time()),
            'comment_modified_gmt' => NULL,
            'honeypot_field' => $fields['akismetHoneypotField'] ?? '',
        ];

        $form = $this->modx->newObject(\AkismetForm::class, $params);

        $client = new Client();
        $akismetCheck = $client->post("https://{$this->apiKey}.rest.akismet.com/1.1/comment-check", [
            'form_params' => $params,
            'http_errors' => false
        ]);
        $spamCheck = (string)$akismetCheck->getBody()->getContents();
        $isSpam = $spamCheck === 'true';

        $form->set('reported_status', $isSpam ? 'spam' : 'notspam');
        if (!$form->save()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Unable to save Akismet spam check data: ' . print_r($params, true));
        }

        if ($isSpam) {
            $this->setError($fields['akismetError'] ?? '');
        }


        return $isSpam;
    }

    /**
     * @param array $params
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function submitSpam(array $params): bool
    {
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
     */
    public function submitHam(array $params): bool
    {
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

    private function setError($message)
    {
        if (empty($message)) {
            $message = $this->modx->lexicon('akismet.message_blocked');
        }

        if ($this->hook instanceof LoginHooks) {
            $this->hook->addError('akismet', $message);
        }
        elseif ($this->hook instanceof fiHooks) {
            $this->hook->addError('akismet', $message);
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

}
