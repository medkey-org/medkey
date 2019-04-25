<?php

use app\modules\medical\assets\ScheduleMedworkerAsset;
use app\modules\medical\MedicalModule;

$this->title = MedicalModule::t('workplace', 'Specialist\'s workplace');
$this->params['breadcrumbs'][] = $this->title;
ScheduleMedworkerAsset::register($this);
?>
<div class="b-block b-block__backdrop">
    <div id="app-workplace"></div>
</div>
<?php $this->registerCssFile(\Yii::getRootAlias('web') . DIRECTORY_SEPARATOR . 'bundles' . DIRECTORY_SEPARATOR . 'style.bundle_medical.css'); ?>
<?php $this->registerJsFile(\Yii::getRootAlias('web') . DIRECTORY_SEPARATOR . 'bundles/bundle_medical.js'); ?>