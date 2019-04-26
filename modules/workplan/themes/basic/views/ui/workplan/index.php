<?php

use app\common\wrappers\Block;
use app\modules\organization\OrganizationModule;
use app\modules\workplan\widgets\grid\WorkplanGrid;

$this->title = \app\modules\workplan\WorkplanModule::t('workplan', 'List of workplans');
$this->params['breadcrumbs'][] = $this->title;

?>

<?= WorkplanGrid::widget([
    'wrapper' => true,
    'wrapperOptions' => [
        'header' => $this->title,
        'wrapperClass' => Block::class
    ],
]);