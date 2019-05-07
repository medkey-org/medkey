<?php

$this->title = \app\modules\config\ConfigModule::t('common', 'Settings');
$this->params['breadcrumbs'][] = $this->title;

echo \app\modules\config\widgets\form\SettingForm::widget();