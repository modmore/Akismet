<?php
/**
 * Akismet for FormIt
 *
 * Copyright 2021 by modmore
 *
 * This snippet integrates Akismet spam protection as a FormIt hook.
 *
 * @var modX $modx
 * @var fiHooks $hook
 *
 */

$path = $modx->getOption('akismet.core_path', null, MODX_CORE_PATH . 'components/akismet/');
$path .= 'vendor/autoload.php';
require_once $path;

use modmore\Akismet\Akismet;
use modmore\Akismet\Exceptions\InvalidAPIKeyException;

//$modx->log(1, print_r($hook->getValues(), true));
//$modx->log(1, print_r($hook->config, true));

try {
    $akismet = new Akismet($modx, $hook);
    if ($akismet->checkSpam()) {
        return true;
    }
    return false;
}
catch(InvalidAPIKeyException $e) {
    $this->modx->log(modX::LOG_LEVEL_ERROR, 'Akismet API key not found. Please add it in the MODX system settings.');
}

return false;