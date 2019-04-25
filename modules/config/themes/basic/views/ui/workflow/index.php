<?php

use app\modules\config\ConfigModule;
use app\modules\config\widgets\grid\WorkflowGrid;
use app\modules\config\assets\ConfigAsset;
use app\common\wrappers\Block;

$this->title = ConfigModule::t('workflow', 'Workflow list');
$this->params['breadcrumbs'][] = $this->title;

ConfigAsset::register($this);
?>

<?= WorkflowGrid::widget([
    'wrapper' => true,
    'wrapperOptions' => [
        'wrapperClass' => Block::class,
        'header' => $this->title,
    ],
]); ?>