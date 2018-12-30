<?php

use app\modules\medical\MedicalModule;
use app\modules\medical\widgets\card\ServicePriceListCard;

$this->title = MedicalModule::t('common', 'Medkey');
$this->params['breadcrumbs'][] = ['url' => '/medical/ui/service-price-list/index', 'label' => 'Список прайс-листов'];
$this->params['breadcrumbs'][] = 'Прайс-лист';

?>

<?= ServicePriceListCard::widget([
    'model' => $model,
]); ?>