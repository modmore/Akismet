<?php
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

class Akismet {

    /** @var modX $modx */
    public $modx;

    /** @var array $config */
    public $config = [];

    /**
     * The version string, used for cache busting and should be increased with each release.
     */
    const VERSION = '1.3.2-pl';

    public function __construct(modX $modx, array $config = [])
    {
        $this->modx = $modx;

        $corePath = $this->modx->getOption('akismet.core_path', $config, $this->modx->getOption('core_path').'components/akismet/');
        $assetsUrl = $this->modx->getOption('akismet.assets_url', $config, $this->modx->getOption('assets_url').'components/akismet/');
        $assetsPath = $this->modx->getOption('akismet.assets_path', $config, $this->modx->getOption('assets_path').'components/akismet/');
        
        $this->config = array_merge($this->config, [
            'basePath' => $corePath,
            'corePath' => $corePath,
            'modelPath' => $corePath.'model/',
            'processorsPath' => $corePath.'processors/',
            'controllersPath' => $corePath.'controllers/',
            'elementsPath' => $corePath.'elements/',
            'templatesPath' => $corePath.'templates/',
            'assetsPath' => $assetsPath,
            'jsUrl' => $assetsUrl.'js/',
            'cssUrl' => $assetsUrl.'css/',
            'assetsUrl' => $assetsUrl,
            'connectorUrl' => $assetsUrl.'connector.php'
        ], $config);

        $this->modx->addPackage('akismet', $this->config['modelPath']);
    }

    public function getStats(): array
    {
        $spam = $this->_getStat('spam');
        $ham = $this->_getStat('ham');
        return [
            'spam' => number_format($spam),
            'ham' => number_format($ham),
            'spam_rate' => $spam ? number_format(($spam / ($spam + $ham)) * 100) . '%' : '0%',
        ];
    }

    private function _getStat(string $key): int
    {
        $setting = $this->modx->getObject('modSystemSetting', [ 'key' => "akismet.total_{$key}"]);
        if ($setting) {
            return (int)$setting->get('value');
        }
        return 0;
    }
}