<?php

use app\modules\dashboard\port\ui\assets\DashboardAsset;
use app\modules\dashboard\widgets\grid\DashboardGrid;
use app\common\wrappers\Block;

$this->title = 'Рабочие столы';
$this->params['breadcrumbs'][] = $this->title;

DashboardAsset::register($this);


echo DashboardGrid::widget([
    'dataProvider' => $dataProvider,
    'wrapper' => true,
    'wrapperOptions' => [
        'header' => $this->title,
        'wrapperClass' => Block::className()
    ],
]);
