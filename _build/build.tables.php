<?php

require_once dirname(__DIR__) . '/config.core.php';
include_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx = new modX();
$modx->initialize('mgr');
$modx->setLogTarget();

if (!XPDO_CLI_MODE) {
    echo '<pre>';
}

$path = $modx->getOption('akismet.core_path'). 'model/';
$modx->addPackage('akismet', $path);

$modx->setLogTarget();
$manager = $modx->getManager();

$tables = [
    \AkismetForm::class
];
foreach ($tables as $object) {
    $success = $manager->createObjectContainer($object);
    if ($success) {
        $modx->log(modX::LOG_LEVEL_INFO, $object . ' table created successfully.');
    }
    else {
        $modx->log(modX::LOG_LEVEL_ERROR, $object . ' table creation failed!');
    }
}


$modx->log(modX::LOG_LEVEL_INFO, 'Complete!');
if (!XPDO_CLI_MODE) {
    echo '</pre>';
}