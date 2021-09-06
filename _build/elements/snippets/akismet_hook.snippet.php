<?php
/**
 * Akismet Spam Protection Hook for the FormIt and Login MODX extras.
 * https://akismet.com
 *
 * Copyright 2021 by modmore
 * https://modmore.com
 *
 * This snippet integrates Akismet spam protection as a FormIt or Login (Register snippet) hook.
 *
 * @var modX $modx
 * @var fiHooks|LoginHooks $hook
 *
 */

$path = $modx->getOption('akismet.core_path', null, MODX_CORE_PATH . 'components/akismet/');
$path .= 'vendor/autoload.php';
require_once $path;

use GuzzleHttp\Exception\GuzzleException;
use modmore\Akismet\Akismet;
use modmore\Akismet\Exceptions\InvalidAPIKeyException;

try {
    $akismet = new Akismet($modx);
    if ($akismet->checkSpam($hook)) {
        // Spam was found! Prevent form submission from continuing.
        return false;
    }

    // No spam, keep going!
    return true;
}
catch(InvalidAPIKeyException $e) {
    $this->modx->log(modX::LOG_LEVEL_ERROR, 'Akismet API key not found. Please add it in the MODX system settings. Form is submitting without a spam check...');
}
catch (GuzzleException $e) {
    $this->modx->log(modX::LOG_LEVEL_ERROR, $e->getMessage() . ': ' . $e->getTraceAsString());
}
catch (xPDOException $e) {
    $this->modx->log(modX::LOG_LEVEL_ERROR, $e->getMessage() . ': ' . $e->getTraceAsString());
}

// Make sure an exception doesn't prevent the form from submitting.
return true;