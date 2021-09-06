<?php
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

class Akismet {

    /** @var modX $modx */
    public $modx;

    /** @var array $config */
    public $config = [];

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
}