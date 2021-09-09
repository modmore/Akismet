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

            $manager->alterField(AkismetForm::class, 'user_ip');

            $manager->addField(\AkismetForm::class, 'honeypot_field_name');
            $manager->addIndex(\AkismetForm::class, 'honeypot_field_name');
            $manager->addField(\AkismetForm::class, 'honeypot_field_value');
            $manager->addIndex(\AkismetForm::class, 'honeypot_field_value');
            break;
    }
}
return true;