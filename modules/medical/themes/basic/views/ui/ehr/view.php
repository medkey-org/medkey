<?php

/** @var $model \app\modules\medical\models\orm\Referral */

use app\modules\medical\MedicalModule;
use app\modules\medical\widgets\card\EhrCard;

$this->title = MedicalModule::t('ehr', 'EHR №') . ' ' . $model->number;
$this->params['breadcrumbs'][] = ['url' => '/medical/ui/ehr/index', 'label' => MedicalModule::t('ehr', 'EHR registry')];
$this->params['breadcrumbs'][] = $this->title;

?>

<?= EhrCard::widget([
    'model' => $model,
]); ?>