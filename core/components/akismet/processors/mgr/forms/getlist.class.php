<?php
class AkismetFormGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'AkismetForm';
    public $languageTopics = array('akismet:default');
    public $defaultSortField = 'created_at';
    public $defaultSortDirection = 'DESC';
    public $objectType = 'akismet.akismetform';
}
return 'AkismetFormGetListProcessor';