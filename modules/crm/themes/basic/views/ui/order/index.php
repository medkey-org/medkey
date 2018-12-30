<?php

use app\common\wrappers\Block;
use app\modules\crm\widgets\grid\OrderGrid;
use app\modules\crm\CrmModule;
use app\modules\crm\assets\OrderAsset;

$this->title = CrmModule::t('common', 'Medkey');
$this->params['breadcrumbs'][] = 'Список заказов';
OrderAsset::register($this);
?>

<?= OrderGrid::widget([
    'wrapper' => true,
    'wrapperOptions' => [
        'header' => 'Список заказов',
        'wrapperClass' => Block::class
    ],
]) ?>