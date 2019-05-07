<?php

use app\modules\medical\MedicalModule;
use app\modules\medical\widgets\card\EhrRecordCard;

$this->title = MedicalModule::t('ehr', 'EHR record');
$this->params['breadcrumbs'][] = ['url' => '/medical/ui/ehr/index', 'label' => MedicalModule::t('ehr', 'EHR registry')];
$this->params['breadcrumbs'][] = ['url' => '/medical/ui/ehr/view', 'label' => MedicalModule::t('ehr', 'EHR №') . ' ' . $ehr->number];
$this->params['breadcrumbs'][] = $this->title;

?>

<?= EhrRecordCard::widget([
    'model' => $model,
    'ehrId' => $ehr->id,
]); ?>