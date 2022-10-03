<?php

require_once dirname(__DIR__, 2) . '/model/akismet/akismet.class.php';

class AkismetStatsDashboardWidget extends modDashboardWidgetInterface
{
    public static $initialized = false;

    protected $akismet;
    protected $assetsUrl;
    protected $stats = [];
    protected $spamRateInt = 0;

    public function initialize(): void
    {
        $this->akismet = new Akismet($this->modx);

        if (static::$initialized) {
            return;
        }
        static::$initialized = true;

        $this->modx->lexicon->load('akismet:default');
        $this->assetsUrl = $this->akismet->config['assetsUrl'];
        $this->controller->addCss($this->assetsUrl . 'css/mgr/mgr.css?v=' . urlencode(Akismet::VERSION));

        $this->stats = $this->akismet->getStats();

        // Get spam rate without percentage symbol to use in graph rendering
        $this->spamRateInt = (int) trim($this->stats['spam_rate'], '%');

        $this->controller->addHtml(<<<HTML
<script src="{$this->assetsUrl}js/mgr/akismet.js"></script>
<script>
Ext.onReady(function() {
    Ext.applyIf(MODx.lang, {$this->modx->toJSON($this->modx->lexicon->loadCache('akismet'))});
    Akismet.stats = {$this->modx->toJSON($this->stats)};
});
</script>
<style>
    #dashboard-block-{$this->widget->get('id')} .body {
        height: 100%;
    }
</style>
HTML
        );
    }

    public function render(): string
    {
        $this->initialize();
        $this->widget->set('name', $this->modx->lexicon('akismet'));
        return <<<HTML
<div id="akismet{$this->widget->get('id')}" class="akismet-widget">
    <div class="akismet-chart">
        <div class="single-chart">
            <svg viewBox="0 0 36 36" class="akismet-circular-chart blue">
              <path class="akismet-circle-bg"
                d="M18 2.0845
                  a 15.9155 15.9155 0 0 1 0 31.831
                  a 15.9155 15.9155 0 0 1 0 -31.831"
              />
              <path class="akismet-circle"
                stroke-dasharray="{$this->spamRateInt}, 100"
                d="M18 2.0845
                  a 15.9155 15.9155 0 0 1 0 31.831
                  a 15.9155 15.9155 0 0 1 0 -31.831"
              />
              <text x="18" y="19.35" class="akismet-spam-percentage">{$this->stats['spam_rate']}</text>
              <text x="18" y="23.35" class="akismet-spam-percentage-text">{$this->modx->lexicon('akismet.spam')}</text>
            </svg>
        </div>
    </div>
    <div class="akismet-stats-block">
        <div class="akismet-stats-col">
            <span class="akismet-spam-blocked stat">{$this->stats['spam']}</span> {$this->modx->lexicon('akismet.spam_blocked')}
        </div>
        <div class="akismet-stats-col">
            <span class="akismet-real-messages stat">{$this->stats['ham']}</span> {$this->modx->lexicon('akismet.real_messages')}
        </div>
    </div>
</div>
HTML;
    }
}
return 'AkismetStatsDashboardWidget';