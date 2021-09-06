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

    /** @var fiHooks $hook */
    private $hook;

    /** @var string $apiKey */
    private $apiKey;

    /**
     * @throws InvalidAPIKeyException
     */
    public function __construct(modX $modx)
    {
        $this->modx = $modx;
        $this->apiKey = $this->_loadAPIKey();
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

    private function getFields(): array
    {
        $config = [];
        if (get_class($this->hook) === 'LoginHooks') {
            $config = $this->hook->login->controller->config;
        }
        else if (get_class($this->hook) === 'fiHooks') {
            $config = $this->hook->config;
        }

        $values = $this->hook->getValues();

        $fields = [];
        foreach ($config as $key => $param) {
            if (substr($key, 0, 7) === 'akismet') {
                $fields[$key] = $values[$param];
            }
        }
        return $fields;
    }

    /**
     * @param fiHooks|LoginHooks $hook
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws xPDOException
     */
    public function checkSpam($hook): bool
    {
        $this->hook = $hook;

        // Load xPDO package
        if (!$this->modx->addPackage('akismet', dirname(__DIR__) . '/model/')) {
            throw new xPDOException('Unable to load Akismet xPDO package!');
        }

        $fields = $this->getFields();

        $permalink = $this->modx->makeUrl(
            $this->modx->resource->get('id'),
            $this->modx->resource->get('context_key'),
            '', 'full');

        $params = [
            'blog' => $this->modx->getOption('site_url'),
            'blog_lang' => $this->modx->getOption('cultureKey'),
            'user_ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'referrer' => $_SERVER['HTTP_REFERER'],
            'permalink' => $permalink,
            'comment_type' => $fields['akismetType'] ?? '',
            'comment_author' => $fields['akismetAuthor'] ?? '',
            'comment_author_email' => $fields['akismetAuthorEmail'] ?? '',
            'comment_author_url' => $fields['akismetAuthorUrl'] ?? '',
            'comment_content' => $fields['akismetContent'] ?? '',
            'blog_charset' => $this->modx->getOption('modx_charset'),
            'recheck_reason' => '',
            'user_role' => $fields['akismetUserRole'] ?? '',
            'is_test' => $fields['akismetTest'] ?? '',
            'comment_date_gmt' => gmdate("Y-m-d H:i:s", time()),
            'comment_modified_gmt' => NULL,
            'honeypot_field' => $fields['akismetHoneypotField'] ?? '',
        ];

        $this->modx->log(1, print_r($params, true));

        $form = $this->modx->newObject(\AkismetForm::class, $params);

        $client = new Client();
        $akismetCheck = $client->post("https://{$this->apiKey}.rest.akismet.com/1.1/comment-check", [
            'form_params' => $params,
            'http_errors' => false
        ]);
        $spamCheck = (string)$akismetCheck->getBody()->getContents();
        $this->modx->log(1, $spamCheck);
        $errorMsg = 'Unable to save Akismet spam check data: ';
        if ($spamCheck === 'true') {
            $form->set('reported_status', 'spam');
            if (!$form->save()) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, $errorMsg . print_r($params, true));
            }
            return true;
        }
        else {
            $form->set('reported_status', 'notspam');
            if (!$form->save()) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, $errorMsg . print_r($params, true));
            }
            return false;
        }
    }

    public function submitSpam($params): bool
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

    public function submitHam($params): bool
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


}