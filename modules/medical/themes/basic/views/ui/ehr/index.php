<?php

use app\modules\medical\widgets\grid\EhrGrid;
use app\modules\medical\MedicalModule;
use app\common\wrappers\Block;

$this->title = MedicalModule::t('ehr', 'EHR registry');
$this->params['breadcrumbs'][] = $this->title;

?>

<?= EhrGrid::widget([
    'wrapper' => true,
    'wrapperOptions' => [
        'header' => $this->title,
        'wrapperClass' => Block::class
    ],
]);