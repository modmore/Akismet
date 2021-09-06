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
            $modelPath = $modx->getOption('core_path').'components/akismet/model/';
            $modx->addPackage('akismet', $modelPath);
            $manager = $modx->getManager();
            $manager->createObjectContainer(AkismetForm::class);
            break;
    }
}
return true;