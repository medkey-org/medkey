<?php

use app\modules\config\ConfigModule;
use app\modules\config\widgets\grid\WorkflowStatusGrid;
use app\modules\config\assets\ConfigAsset;
use app\common\wrappers\Block;

$this->title = ConfigModule::t('workflow', 'Workflow status list');
$this->params['breadcrumbs'][] = $this->title;

ConfigAsset::register($this);
?>

<?= WorkflowStatusGrid::widget([
    'wrapper' => true,
    'wrapperOptions' => [
        'wrapperClass' => Block::class,
        'header' => $this->title,
    ],
]); ?>