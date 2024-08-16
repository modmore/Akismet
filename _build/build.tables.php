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

// Temporarily change logging level to ignore duplicate column errors
$oldLevel = $modx->setLogLevel(modX::LOG_LEVEL_FATAL);

$manager->addField(\AkismetForm::class, 'honeypot_field_name');
$manager->addIndex(\AkismetForm::class, 'honeypot_field_name');
$manager->addField(\AkismetForm::class, 'honeypot_field_value');
$manager->addIndex(\AkismetForm::class, 'honeypot_field_value');

$modx->setLogLevel($oldLevel);
$modx->log(modX::LOG_LEVEL_INFO, 'Complete!');
if (!XPDO_CLI_MODE) {
    echo '</pre>';
}