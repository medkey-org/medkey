<?php

use app\modules\medical\MedicalModule;
use app\modules\medical\widgets\card\PatientCard;

$this->title = MedicalModule::t('patient','Patient');
$this->params['breadcrumbs'][] = ['url' => '/medical/ui/patient/index', 'label' => MedicalModule::t('patient','Patient registry')];
$this->params['breadcrumbs'][] = $this->title;

echo PatientCard::widget([
    'model' => $model->id,
]);

$this->registerJsFile(\Yii::getRootAlias('web') . DIRECTORY_SEPARATOR . 'bundles/bundle_medical.js');
$this->registerCssFile(\Yii::getRootAlias('web') . DIRECTORY_SEPARATOR . 'bundles/style.bundle_medical.css');