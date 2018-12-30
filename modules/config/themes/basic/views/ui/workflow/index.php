<?php

use app\modules\config\ConfigModule;
use app\modules\config\widgets\grid\WorkflowGrid;
use app\modules\config\assets\ConfigAsset;
use app\common\wrappers\Block;

$this->title = 'ЖЦ';
$this->params['breadcrumbs'][] = 'Список ЖЦ';

ConfigAsset::register($this);
?>

<?= WorkflowGrid::widget([
    'wrapper' => true,
    'wrapperOptions' => [
        'wrapperClass' => Block::class,
        'header' => 'Список ЖЦ',
    ],
]); ?>