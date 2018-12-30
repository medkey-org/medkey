<?php

use app\common\wrappers\Block;
use app\modules\organization\OrganizationModule;
use app\modules\organization\widgets\grid\EmployeeGrid;

$this->title = 'Сотрудники';
$this->params['breadcrumbs'][] = OrganizationModule::t('common', 'Employees');
?>

<?= EmployeeGrid::widget([
    'wrapper' => true,
    'wrapperOptions' => [
        'header' => OrganizationModule::t('common', 'List of employees'),
        'wrapperClass' => Block::class
    ],
]) ?>