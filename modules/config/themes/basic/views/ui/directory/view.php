<?php

/** @var string $key */
/** @var array $directory */

use app\modules\config\assets\ConfigAsset;
use app\modules\config\widgets\grid\DirectoryEntityGrid;
use app\common\helpers\Html;
use app\modules\config\ConfigModule;

$this->title = ConfigModule::t('common', 'Directories');
$this->params['breadcrumbs'][] = [
    'label' => ConfigModule::t('common', 'Directories'),
    'url' => ['/config/ui/directory'],
];
$this->params['breadcrumbs'][] = [
    'label' => Html::encode($directory['label'])
];
ConfigAsset::register($this);
?>

<?= DirectoryEntityGrid::widget([
    'key' => $key,
]); ?>