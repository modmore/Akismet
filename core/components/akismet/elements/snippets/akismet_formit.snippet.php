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

try {
    $akismet = new Akismet($modx, $hook);
    if ($akismet->checkSpam()) {
        // Spam was found! Prevent form submission from continuing.
        return false;
    }

    // No spam, keep going!
    return true;
}
catch(InvalidAPIKeyException $e) {
    $this->modx->log(modX::LOG_LEVEL_ERROR, 'Akismet API key not found. Please add it in the MODX system settings. Form is submitting without a spam check...');
}

// Make sure a missing API key doesn't prevent the form from submitting.
return true;