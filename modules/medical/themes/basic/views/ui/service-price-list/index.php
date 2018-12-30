<?php
use app\modules\medical\widgets\grid\ServicePriceListGrid;
use app\common\wrappers\Block;

$this->title = 'Medkey';
$this->params['breadcrumbs'][] = 'Список прайс-листов';

?>

<?= ServicePriceListGrid::widget([
    'wrapper' => true,
    'wrapperOptions' => [
        'header' => 'Список прайс-листов',
        'wrapperClass' => Block::class
    ],
]);