<?php
class AkismetFormRemoveProcessor extends modObjectRemoveProcessor {
    public $classKey = AkismetForm::class;
    public $languageTopics = array('akismet:default');
    public $objectType = 'akismet.akismetform';
}
return 'AkismetFormRemoveProcessor';