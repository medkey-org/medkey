<?php

use app\modules\medical\MedicalModule;
use app\modules\medical\widgets\card\PatientCard;

$this->title = MedicalModule::t('patient','Patient card');
$this->params['breadcrumbs'][] = ['url' => '/medical/ui/patient/index', 'label' => MedicalModule::t('patient','Patient registry')];
$this->params['breadcrumbs'][] = $this->title;

?>

<?= PatientCard::widget([
    'model' => $model->id,
]); ?>