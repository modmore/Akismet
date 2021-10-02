<?php

/**
 * @param string $filename The name of the file.
 * @return string The file's content
 * @by splittingred
 */
function getSnippetContent($filename = '') {
    $o = file_get_contents($filename);
    $o = str_replace('<?php','',$o);
    $o = str_replace('?>','',$o);
    $o = trim($o);
    return $o;
}

$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

if (!defined('MOREPROVIDER_BUILD')) {
    /* define version */
    define('PKG_NAME', 'Akismet');
    define('PKG_NAMESPACE', 'akismet');
    define('PKG_VERSION', '1.2.0');
    define('PKG_RELEASE', 'pl');

    /* load modx */
    require_once dirname(__DIR__) . '/config.core.php';
    require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
    $modx= new modX();
    $modx->initialize('mgr');
    $modx->setLogLevel(modX::LOG_LEVEL_INFO);
    $modx->setLogTarget('ECHO');
    $targetDirectory = dirname(__DIR__) . '/_packages/';
}
else {
    $targetDirectory = MOREPROVIDER_BUILD_TARGET;
}

$root = dirname(__DIR__).'/';
$sources = [
    'root' => $root,
    'build' => $root .'_build/',
    'events' => $root . '_build/events/',
    'resolvers' => $root . '_build/resolvers/',
    'validators' => $root . '_build/validators/',
    'data' => $root . '_build/data/',
    'plugins' => $root.'_build/elements/plugins/',
    'snippets' => $root.'_build/elements/snippets/',
    'source_core' => $root.'core/components/'.PKG_NAMESPACE,
    'source_assets' => $root.'assets/components/'.PKG_NAMESPACE,
    'lexicon' => $root . 'core/components/'.PKG_NAMESPACE.'/lexicon/',
    'docs' => $root.'core/components/'.PKG_NAMESPACE.'/docs/',
    'model' => $root.'core/components/'.PKG_NAMESPACE.'/model/',
];
unset($root);

$modx->loadClass('transport.modPackageBuilder','',false, true);
$builder = new modPackageBuilder($modx);
$builder->directory = $targetDirectory;
$builder->createPackage(PKG_NAMESPACE,PKG_VERSION,PKG_RELEASE);
$builder->registerNamespace(PKG_NAMESPACE,false,true,'{core_path}components/'.PKG_NAMESPACE.'/', '{assets_path}components/'.PKG_NAMESPACE.'/');

$modx->log(modX::LOG_LEVEL_INFO,'Packaged in namespace.'); flush();

$builder->package->put(
    [
        'source' => $sources['source_core'],
        'target' => "return MODX_CORE_PATH . 'components/';",
    ],
    [
        xPDOTransport::ABORT_INSTALL_ON_VEHICLE_FAIL => true,
        'vehicle_class' => 'xPDOFileVehicle',
        'validate' => [
            [
                'type' => 'php',
                'source' => $sources['validators'] . 'requirements.script.php'
            ]
        ],
        'resolve' => [
            [
                'type' => 'php',
                'source' => $sources['resolvers'] . 'dependencies.resolver.php',
            ],
            [
                'type' => 'php',
                'source' => $sources['resolvers'] . 'tables.resolver.php',
            ],
        ]
    ]
);
$modx->log(modX::LOG_LEVEL_INFO,'Packaged in core, requirements validator, and module loading resolver.'); flush();

/**
 * Assets
 */
$builder->package->put(
    [
        'source' => $sources['source_assets'],
        'target' => "return MODX_ASSETS_PATH . 'components/';",
    ],
    [
        'vehicle_class' => 'xPDOFileVehicle',
    ]
);
$modx->log(modX::LOG_LEVEL_INFO,'Packaged in assets.'); flush();

/**
 * Settings
 */
$settings = include $sources['data'] . 'transport.settings.php';
if (is_array($settings)) {
    $attributes = [
        xPDOTransport::UNIQUE_KEY => 'key',
        xPDOTransport::PRESERVE_KEYS => true,
        xPDOTransport::UPDATE_OBJECT => false,
    ];
    foreach ($settings as $setting) {
        $vehicle = $builder->createVehicle($setting,$attributes);
        $builder->putVehicle($vehicle);
    }
    $modx->log(modX::LOG_LEVEL_INFO,'Packaged in ' . count($settings) . ' system settings.'); flush();
    unset($settings,$setting,$attributes);
}

/**
 * Menu
 */
$menu = include $sources['data'].'transport.menu.php';
if (empty($menu)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Could not package in menu.');
} else {
    $menuVehicle = $builder->createVehicle($menu, array(
        xPDOTransport::PRESERVE_KEYS => true,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::UNIQUE_KEY    => 'text'
    ));
    $builder->putVehicle($menuVehicle);
    $modx->log(modX::LOG_LEVEL_INFO, 'Packaged in Menu');
    unset($menuVehicle, $menu);
}

/**
 * Category
 */
$category= $modx->newObject('modCategory');
$category->set('category','Akismet');
$modx->log(modX::LOG_LEVEL_INFO,'Created category.');

/**
 * Snippets
 */
$snippetSource = include $sources['data'].'snippets.php';
$snippets = [];
foreach($snippetSource as $name => $options) {
    $snippets[$name] = $modx->newObject('modSnippet');
    $snippets[$name]->fromArray([
        'name' => $name,
        'description' => $options['description'],
        'snippet' => getSnippetContent($sources['snippets'].$options['file']),
    ],'',true,true);
}
$category->addMany($snippets);
unset($snippets);
$modx->log(modX::LOG_LEVEL_INFO,'Packaged in snippets.');

$attr = [
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => [
        'Snippets' => [
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ],
    ]
];

$vehicle = $builder->createVehicle($category,$attr);

$vehicle->resolve('php', [
    'source' => $sources['resolvers'] . 'counter.resolver.php',
]);

$builder->putVehicle($vehicle);

/* now pack in the license file, readme and setup options */
$builder->setPackageAttributes([
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
    'changelog' => file_get_contents($sources['docs'] . 'changelog.txt'),
]);
$modx->log(modX::LOG_LEVEL_INFO,'Packaged in package attributes.'); flush();

$modx->log(modX::LOG_LEVEL_INFO,'Zipping up the package...'); flush();
$builder->pack();

$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tend = $mtime;
$totalTime = ($tend - $tstart);
$totalTime = sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO,"\nPackage Built.\nExecution time: {$totalTime}\n");

