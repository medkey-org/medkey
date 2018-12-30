<?php

use app\common\wrappers\Block;
use app\modules\organization\OrganizationModule;
use app\modules\workplan\widgets\grid\WorkplanGrid;

$this->title = OrganizationModule::t('common', 'Medkey');
$this->params['breadcrumbs'][] = 'Список рабочих планов';

?>

<?= WorkplanGrid::widget([
    'wrapper' => true,
    'wrapperOptions' => [
        'header' => 'Список рабочих планов',
        'wrapperClass' => Block::class
    ],
]);