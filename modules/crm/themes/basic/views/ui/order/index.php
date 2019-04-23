<?php

use app\common\wrappers\Block;
use app\modules\crm\widgets\grid\OrderGrid;
use app\modules\crm\CrmModule;
use app\modules\crm\assets\OrderAsset;

$this->title = CrmModule::t('order', 'Order list');
$this->params['breadcrumbs'][] = $this->title;
OrderAsset::register($this);
?>

<?= OrderGrid::widget([
    'wrapper' => true,
    'wrapperOptions' => [
        'header' => $this->title,
        'wrapperClass' => Block::class,
    ],
]) ?>