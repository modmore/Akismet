<?php
/**
 * Akismet Spam Protection Hook for the FormIt, Login, and Quip MODX extras.
 * https://akismet.com
 *
 * Copyright 2021 by modmore
 * https://modmore.com
 *
 * This snippet integrates Akismet spam protection as a FormIt, Login (Register snippet), or Quip hook.
 *
 * @var modX $modx
 * @var fiHooks|LoginHooks|quipHooks $hook
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

    switch (get_class($hook)) {
        case fiHooks::class:
            $config = $hook->config;
            break;
        case LoginHooks::class:
            $config = $hook->login->controller->config;
            break;
        case quipHooks::class:
            $config = $hook->quip->config;
            break;
        default:
            $modx->log(modX::LOG_LEVEL_ERROR, '[Akismet] Invalid hook provided when attempting to analyse spam. Submitting form without a spam check...');
            return true;
    }

    if ($akismet->checkSpam($hook->getValues(), $config)) {
        // Spam was found! Prevent form submission from continuing.
        $message = $config['akismetError'] ?? $modx->lexicon('akismet.message_blocked');
        $hook->addError('akismet', $message);

        return false;
    }

    // No spam, keep going!
    return true;
}
catch(InvalidAPIKeyException $e) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Akismet API key not found. Please add it in the MODX system settings. Form is submitting without a spam check...');
}
catch (GuzzleException $e) {
    $modx->log(modX::LOG_LEVEL_ERROR, $e->getMessage() . ': ' . $e->getTraceAsString());
}
catch (xPDOException $e) {
    $modx->log(modX::LOG_LEVEL_ERROR, $e->getMessage() . ': ' . $e->getTraceAsString());
}

// Make sure an exception doesn't prevent the form from submitting.
return true;