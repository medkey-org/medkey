<?php

use app\modules\security\assets\SecurityAsset;
use app\modules\medical\widgets\grid\ReferralGrid;
use app\modules\medical\MedicalModule;
use app\common\wrappers\Block;

$this->title = 'Medkey';
$this->params['breadcrumbs'][] = 'Список направлений';

?>

<?= ReferralGrid::widget([
    'wrapper' => true,
    'wrapperOptions' => [
        'header' => 'Список направлений',
        'wrapperClass' => Block::class,
    ],
]);