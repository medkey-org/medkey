<?php

use app\modules\medical\widgets\grid\AttendanceGrid;
use app\modules\medical\MedicalModule;
use app\common\wrappers\Block;

$this->title =  MedicalModule::t('attendance','Attendance registry');
$this->params['breadcrumbs'][] = $this->title;

?>

<?= AttendanceGrid::widget([
    'wrapper' => true,
    'wrapperOptions' => [
        'header' => $this->title,
        'wrapperClass' => Block::class,
    ],
]);