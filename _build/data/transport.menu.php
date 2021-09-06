<?php
/**
 * @var modX $modx
 */
$menu = $modx->newObject('modMenu');
$menu->fromArray(array(
    'text' => 'akismet',
    'parent' => 'components',
    'action' => 'home',
    'description' => 'akismet.menu_desc',
    'namespace' => 'akismet',
), '', true, true);
return $menu;