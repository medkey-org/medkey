<?php

use app\modules\medical\MedicalModule;
use app\modules\medical\widgets\card\PatientCard;

$this->title = MedicalModule::t('common', 'Medkey');
$this->params['breadcrumbs'][] = ['url' => '/medical/ui/patient/index', 'label' => 'Список пациентов'];
$this->params['breadcrumbs'][] = 'Карточка пациента';

?>

<?= PatientCard::widget([
    'model' => $model->id,
]); ?>