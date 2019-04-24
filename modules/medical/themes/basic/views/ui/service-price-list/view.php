<?php

use app\modules\medical\MedicalModule;
use app\modules\medical\widgets\card\ServicePriceListCard;

$this->title = MedicalModule::t('servicePriceList', 'Price-list card');
$this->params['breadcrumbs'][] = ['url' => '/medical/ui/service-price-list/index', 'label' => MedicalModule::t('servicePriceList', 'Price-list registry')];
$this->params['breadcrumbs'][] = $this->title;

?>

<?= ServicePriceListCard::widget([
    'model' => $model,
]); ?>