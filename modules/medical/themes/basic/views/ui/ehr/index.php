<?php

use app\modules\medical\widgets\grid\EhrGrid;
use app\modules\medical\MedicalModule;
use app\common\wrappers\Block;

$this->title = 'Medkey';
$this->params['breadcrumbs'][] = 'Список медицинских карт';

?>

<?= EhrGrid::widget([
    'wrapper' => true,
    'wrapperOptions' => [
        'header' => 'Список медицинских карт',
        'wrapperClass' => Block::class
    ],
]);