<?php
use app\modules\medical\widgets\grid\ServicePriceListGrid;
use app\common\wrappers\Block;
use app\modules\medical\MedicalModule;

$this->title = MedicalModule::t('servicePriceList', 'Pricelist registry');
$this->params['breadcrumbs'][] = $this->title;

?>

<?= ServicePriceListGrid::widget([
    'wrapper' => true,
    'wrapperOptions' => [
        'header' => $this->title,
        'wrapperClass' => Block::class
    ],
]);