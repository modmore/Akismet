<?php

use modmore\Akismet\Akismet;
use modmore\Akismet\Exceptions\InvalidAPIKeyException;

class AkismetMarkSpamUpdateProcessor extends modObjectUpdateProcessor {
    public $classKey = \AkismetForm::class;
    public $languageTopics = array('akismet:default');
    public $objectType = 'akismet.akismetform';
    private $akismet;

    public function initialize()
    {
        try {
            $this->akismet = new Akismet($this->modx);
        }
        catch (InvalidAPIKeyException $e) {
            return false;
        }
        return parent::initialize();
    }

    public function beforeSave(): bool
    {
        $params = $this->object->toArray();
        $remove = ['id', 'action', 'reported_status','manual_status'];
        foreach ($params as $k => $v) {
            if (in_array($k, $remove)) {
                unset($params[$k]);
            }
        }
        if ($this->akismet->submitSpam($params)) {
            $this->object->set('manual_status', 'spam');
            $this->object->set('updated_at', time());
        }
        return parent::beforeSave();
    }
}
return 'AkismetMarkSpamUpdateProcessor';