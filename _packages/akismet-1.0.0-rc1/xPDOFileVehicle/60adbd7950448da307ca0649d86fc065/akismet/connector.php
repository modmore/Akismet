<?php
/**
 * @var modX $modx
 */
require_once dirname(__FILE__, 4) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('akismet.core_path',null,$modx->getOption('core_path').'components/akismet/');
require_once $corePath.'model/akismet/akismet.class.php';

$modx->akismet = new Akismet($modx);
$modx->lexicon->load('akismet:default');

$path = $modx->getOption('processorsPath', $modx->akismet->config,$corePath . 'processors/');
$modx->request->handleRequest([
    'processors_path' => $path,
    'location' => '',
]);