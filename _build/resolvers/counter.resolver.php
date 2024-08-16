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

            // Fix dashboard widget sizing in MODX 2.x for previous installs
            $modxVersion = $modx->getVersionData();
            if (version_compare($modxVersion['full_version'], '3.0.0-dev', '<')) {
                $widgets = $modx->getCollection(modDashboardWidget::class, [
                    'namespace' => 'akismet',
                ]);
                foreach ($widgets as $widget) {
                    if ($widget->get('size') === 'one-third') {
                        $widget->set('size', 'half');
                        $widget->save();
                    }
                }
            }

            break;
    }
}
return true;