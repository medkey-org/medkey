<?php

use app\common\helpers\Html;
use app\common\web\View;
use app\modules\medical\MedicalModule;
use app\common\wrappers\Block;

$this->title =  MedicalModule::t('attendance','Attendance registry');
$this->params['breadcrumbs'][] = $this->title;

$content = Html::beginDiv(['id' => 'schedule'])
. Html::endDiv()
. \Yii::$app->view->registerJs(<<<JS
        registerSchedule(1, $duration);
JS
    , View::POS_END);

echo Block::widget(['wrapperContent'=> $content]);
$this->registerJsFile(\Yii::getRootAlias('web') . DIRECTORY_SEPARATOR . 'bundles/bundle_medical.js');
$this->registerCssFile(\Yii::getRootAlias('web') . DIRECTORY_SEPARATOR . 'bundles/style.bundle_medical.css');