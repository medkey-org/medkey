<?php

use app\modules\medical\widgets\grid\AttendanceGrid;
use app\modules\medical\MedicalModule;
use app\common\wrappers\Block;

$this->title = 'Medkey';
$this->params['breadcrumbs'][] = 'Список записей';

?>

<?= AttendanceGrid::widget([
    'wrapper' => true,
    'wrapperOptions' => [
        'header' => 'Список записей',
        'wrapperClass' => Block::class
    ],
]);