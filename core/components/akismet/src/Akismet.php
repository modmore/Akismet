<?php

namespace modmore\Akismet;

use modX;
use xPDOException;
use fiHooks;
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
     * @throws xPDOException
     */
    public function __construct(modX $modx, fiHooks $hook)
    {
        $this->modx = $modx;
        $this->hook = $hook;
        $this->apiKey = $this->_loadAPIKey();

        // Load xPDO package
        if (!$this->modx->addPackage(\Akismet::class, dirname(__DIR__), 2) . 'model/') {
            throw new xPDOException('Unable to load \Akismet xPDO package!');
        }
    }

    /**
     * @throws InvalidAPIKeyException
     */
    private function _loadAPIKey()
    {
        $apiKey = $this->modx->getOption('akismet.api_key');
        if (!$apiKey) {
            throw new InvalidAPIKeyException('Invalid API Key.');
        }
        return $apiKey;
    }

    private function getFields(): array
    {
        $values = $this->hook->getValues();
        $fields = [];
        foreach ($this->hook->config as $key => $param) {
            if (substr($key, 0, 7) === 'akismet') {
                $fields[$key] = $values[$param];
            }
        }
        return $fields;
    }

    public function checkSpam(): bool
    {

        $fields = $this->getFields();

        $params = [
            'blog' => $this->modx->getOption('site_url'),
            'blog_lang' => $this->modx->getOption('cultureKey'),
            'user_ip' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'comment_type' => $fields['akismetType'] ?? '',
            'comment_author' => $fields['akismetAuthor'] ?? '',
            'comment_author_email' => $fields['akismetAuthorEmail'] ?? '',
            'comment_author_url' => $fields['akismetAuthorUrl'] ?? '',
            'comment_content' => $fields['akismetContent'] ?? '',
        ];

        $this->modx->log(1,print_r($params, true));

        $client = new Client();
        $akismetCheck = $client->post("https://{$this->apiKey}.rest.akismet.com/1.1/comment-check", [
            'form_params' => $params,
            'http_errors' => false
        ]);
        $spamCheck = (string)$akismetCheck->getBody()->getContents();
        if ($spamCheck === 'true') {
            $this->modx->log(1,'spam!');
            // it's spam!
        }
        else {
            $this->modx->log(1,'not spam!');
        }

        return true;
    }

    public function submitSpam()
    {

    }

    public function submitHam()
    {

    }


}