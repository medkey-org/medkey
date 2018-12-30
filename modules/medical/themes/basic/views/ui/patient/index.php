<?php

use app\modules\medical\widgets\grid\PatientGrid;
use app\modules\medical\MedicalModule;
use app\common\wrappers\Block;

$this->title = 'Medkey';
$this->params['breadcrumbs'][] = 'Список пациентов'

?>

<?= PatientGrid::widget([
    'wrapper' => true,
    'wrapperOptions' => [
        'header' => 'Список пациентов',
        'wrapperClass' => Block::class
    ],
]);