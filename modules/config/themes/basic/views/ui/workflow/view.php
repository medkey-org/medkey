<?php

use app\modules\config\ConfigModule;

$this->title = ConfigModule::t('workflow', 'Workflow');
$this->params['breadcrumbs'][] = [
    'label' => ConfigModule::t('workflow', 'Workflow list'),
    'url' => ['/config/ui/workflow'],
];
$this->params['breadcrumbs'][] = $this->title;

echo \app\modules\config\widgets\misc\WorkflowTransitionWidget::widget(['workflowId' => $workflowId]);