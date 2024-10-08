<?php
/**
 * Akismet Dashboard Widgets
 *
 * @package akismet
 * @subpackage build
 *
 * @var modX $modx
 *
 */

// Use different base paths if we're bootstrapping rather than building.
if (isset($componentPath)) {
    $basePath = $componentPath . '/core/';
}
else {
    $basePath = '[[++core_path]]';
}

$widgets = [];
$widgets[0]= $modx->newObject(modDashboardWidget::class);
$widgets[0]->fromArray([
    'name' => 'akismet.widget.menu_name',
    'description' => 'akismet.widget.menu_desc',
    'type' => 'file',
    'size' => 'half',
    'content' => $basePath . 'components/akismet/elements/widgets/akismetstats.class.php',
    'namespace' => 'akismet',
    'lexicon' => 'akismet:default',
], '', true, true);

return $widgets;