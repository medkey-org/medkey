<?php

/** @var \app\modules\crm\models\orm\Order $model */

use app\modules\crm\CrmModule;
use app\modules\crm\widgets\card\OrderCard;
use app\modules\crm\assets\OrderAsset;

$this->title = CrmModule::t('common', 'Medkey');
$this->params['breadcrumbs'][] = ['url' => '/crm/ui/order/index', 'label' => 'Список заказов'];
$this->params['breadcrumbs'][] = 'Карточка заказа';
OrderAsset::register($this);
?>

<?= OrderCard::widget([
    'model' => $model
]); ?>