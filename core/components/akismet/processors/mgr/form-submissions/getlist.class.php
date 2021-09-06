<?php
class AkismetFormGetListProcessor extends modObjectGetListProcessor {
    public $classKey = \AkismetForm::class;
    public $languageTopics = array('akismet:default');
    public $defaultSortField = 'created_at';
    public $defaultSortDirection = 'DESC';
    public $objectType = 'akismet.akismetform';

    /**
     * @param xPDOQuery $c
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c): xPDOQuery
    {
        $query = $this->getProperty('query');
        if (!empty($query)) {
            $c->where([
                'created_at:LIKE' => '%'.$query.'%',
                'OR:comment_author:LIKE' => '%'.$query.'%',
                'OR:comment_author_email:LIKE' => '%'.$query.'%',
                'OR:comment_author_url:LIKE' => '%'.$query.'%',
                'OR:user_ip:LIKE' => '%'.$query.'%',
                'OR:user_agent:LIKE' => '%'.$query.'%',
            ]);
        }
        return $c;
    }
}
return 'AkismetFormGetListProcessor';