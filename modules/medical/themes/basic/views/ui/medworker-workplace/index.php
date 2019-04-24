<?php
use app\modules\medical\assets\ScheduleMedworkerAsset;
$this->title = \app\modules\medical\MedicalModule::t('workplace', 'Specialist\'s workplace');
$this->params['breadcrumbs'][] = $thss->title;
ScheduleMedworkerAsset::register($this);
?>
<div class="b-block b-block__backdrop">
    <div id="app-workplace"></div>
</div>