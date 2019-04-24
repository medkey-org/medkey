<?php

/** @var $model \app\modules\medical\models\orm\Referral */

use app\modules\medical\MedicalModule;
use app\modules\medical\widgets\card\AttendanceCard;

$this->title = MedicalModule::t('attendance', 'Attendance record');
$this->params['breadcrumbs'][] = ['url' => '/medical/ui/attendance/index', 'label' => MedicalModule::t('attendance', 'Attendance registry')];
$this->params['breadcrumbs'][] =  $this->title;

?>

<?= AttendanceCard::widget([
    'model' => $model,
]); ?>