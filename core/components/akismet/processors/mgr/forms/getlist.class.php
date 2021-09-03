<?php
class AkismetFormGetListProcessor extends modObjectGetListProcessor {
    public $classKey = \AkismetForm::class;
    public $languageTopics = array('akismet:default');
    public $defaultSortField = 'created_at';
    public $defaultSortDirection = 'DESC';
    public $objectType = \AkismetForm::class . '.akismetform';

    public function prepareQueryBeforeCount(xPDOQuery $c): xPDOQuery
    {
        //
        return $c;
    }
}
return 'AkismetFormGetListProcessor';