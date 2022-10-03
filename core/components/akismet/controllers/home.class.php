<?php

require_once dirname(__DIR__) . '/model/akismet/akismet.class.php';

class AkismetHomeManagerController extends modExtraManagerController
{
    /** @var Akismet $akismet */
    protected $akismet;

    public function getPageTitle()
    {
        return $this->modx->lexicon('akismet');
    }

    public function getLanguageTopics()
    {
        return ['akismet:default'];
    }

    public function initialize()
    {
        $this->akismet = new Akismet($this->modx);
        $this->addJavascript($this->akismet->config['jsUrl'] . 'mgr/akismet.js');
        $this->addHtml('<script type="text/javascript">
            Ext.onReady(function() {
                Akismet.config = '.$this->modx->toJSON($this->akismet->config).';
                Akismet.stats = '. $this->modx->toJSON($this->akismet->getStats()) . ';
            });
            </script>');
    }

    public function loadCustomCssJs()
    {
        $this->addJavascript($this->akismet->config['jsUrl'] . 'mgr/form-submission.window.js');
        $this->addJavascript($this->akismet->config['jsUrl'] . 'mgr/form-submissions.grid.js');
        $this->addJavascript($this->akismet->config['jsUrl'] . 'mgr/akismet.panel.js');
        $this->addLastJavascript($this->akismet->config['jsUrl'] . 'mgr/akismet.page.js');
        $this->addCss($this->akismet->config['cssUrl'] . 'mgr/mgr.css');
    }

    public function getTemplateFile()
    {
        return $this->akismet->config['templatesPath'] . 'home.tpl';
    }

}