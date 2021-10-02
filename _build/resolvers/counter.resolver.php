<?php
/**
 * @var modX $modx
 * @var xPDOTransportVehicle $transport
 * @var array $options
 */

if ($transport->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_UPGRADE:
        case xPDOTransport::ACTION_INSTALL:
            $modx =& $transport->xpdo;

            $setting = $modx->getObject('modSystemSetting', [ 'key' => 'akismet.total_spam']);
            if ($setting && $setting->get('value') < 1) {
                $count = $modx->getCount(AkismetForm::class, ['reported_status' => 'spam']);
                $setting->set('value', $count);
                $setting->save();
            }

            $setting = $modx->getObject('modSystemSetting', [ 'key' => 'akismet.total_ham']);
            if ($setting && $setting->get('value') < 1) {
                $count = $modx->getCount(AkismetForm::class, ['reported_status' => 'notspam']);
                $setting->set('value', $count);
                $setting->save();
            }

            break;
    }
}
return true;