<?php

use app\modules\config\ConfigModule;
use app\modules\config\widgets\grid\WorkflowStatusGrid;
use app\modules\config\assets\ConfigAsset;
use app\common\wrappers\Block;

$this->title = 'Статусы ЖЦ';
$this->params['breadcrumbs'][] = 'Список статусов ЖЦ';

ConfigAsset::register($this);
?>

<?= WorkflowStatusGrid::widget([
    'wrapper' => true,
    'wrapperOptions' => [
        'wrapperClass' => Block::class,
        'header' => 'Список статусов ЖЦ',
    ],
]); ?>