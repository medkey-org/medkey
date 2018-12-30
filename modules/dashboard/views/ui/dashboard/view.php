<?php

use app\modules\dashboard\port\ui\assets\DashboardAsset;
use app\modules\dashboard\widgets\card\DashboardCard;
use app\modules\dashboard\models\orm\Dashboard;

/**
 * @var Dashboard   $model
 */
$this->title = $model->isNewRecord ? 'Создать рабочий стол' : $model->title;
$this->params['breadcrumbs'][] = ['url' => '/dashboard/ui/dashboard/list', 'label' => 'Рабочие столы'];
$this->params['breadcrumbs'][] = $model->isNewRecord ? 'Создать рабочий стол' : $model->title;

DashboardAsset::register($this);

echo DashboardCard::widget([
    'model' => $model->id,
]);
