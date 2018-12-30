<?php

/** @var $model \app\modules\medical\models\orm\Referral */

use app\modules\medical\MedicalModule;
use app\modules\medical\widgets\card\AttendanceCard;

$this->title = MedicalModule::t('common', 'Medkey');
$this->params['breadcrumbs'][] = ['url' => '/medical/ui/attendance/index', 'label' => 'Список записей'];
$this->params['breadcrumbs'][] = 'Карточка записи';

?>

<?= AttendanceCard::widget([
    'model' => $model,
]); ?>