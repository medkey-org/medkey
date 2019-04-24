<?php

use app\modules\medical\widgets\grid\PatientGrid;
use app\modules\medical\MedicalModule;
use app\common\wrappers\Block;

$this->title = MedicalModule::t('patient','Patient registry');
$this->params['breadcrumbs'][] = $this->title;

?>

<?= PatientGrid::widget([
    'wrapper' => true,
    'wrapperOptions' => [
        'header' => $this->title,
        'wrapperClass' => Block::class
    ],
]);