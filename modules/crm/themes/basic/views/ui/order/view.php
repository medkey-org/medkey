<?php

/** @var \app\modules\crm\models\orm\Order $model */

use app\modules\crm\CrmModule;
use app\modules\crm\widgets\card\OrderCard;
use app\modules\crm\assets\OrderAsset;

$this->title = CrmModule::t('order', 'Order list');
$this->params['breadcrumbs'][] = ['url' => '/crm/ui/order/index', 'label' => $this->title];
$this->params['breadcrumbs'][] = CrmModule::t('order', 'Order card');
OrderAsset::register($this);
?>

<?= OrderCard::widget([
    'model' => $model
]); ?>