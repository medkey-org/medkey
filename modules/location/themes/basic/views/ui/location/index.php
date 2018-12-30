<?php

use app\common\wrappers\Block;
use app\modules\location\widgets\grid\LocationGrid;
use app\modules\location\LocationModule;

$this->title = LocationModule::t('common', 'Medkey');
$this->params['breadcrumbs'][] = LocationModule::t('common', 'Locations');
?>

<?= LocationGrid::widget([
    'wrapper' => true,
    'wrapperOptions' => [
        'header' => LocationModule::t('common', 'List of locations'),
        'wrapperClass' => Block::className()
    ],
]) ?>