<?php

use app\modules\medical\MedicalModule;
use app\modules\medical\widgets\card\EhrRecordCard;
use app\common\helpers\Url;
use app\common\helpers\Html;

$this->title = MedicalModule::t('ehr', 'EHR record');
$this->params['breadcrumbs'][] = ['url' => Url::to(['/medical/ui/ehr/view']), 'label' => MedicalModule::t('ehr', 'EHR registry')];
$this->params['breadcrumbs'][] = ['url' => Url::to(['/medical/ui/ehr/view', 'id' => Html::encode($ehr->id)]), 'label' => MedicalModule::t('ehr', 'EHR № ') . Html::encode($ehr->number)];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= EhrRecordCard::widget([
    'model' => $model,
    'ehrId' => $ehr->id,
]); ?>