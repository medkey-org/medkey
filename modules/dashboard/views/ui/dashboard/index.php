<?php

use app\modules\dashboard\port\ui\assets\DashboardAsset;
use app\modules\dashboard\widgets\misc\UserDashboardTab;

$this->title = \app\modules\dashboard\DashboardModule::t('dashboard', 'My dashboards');

DashboardAsset::register($this);

echo UserDashboardTab::widget();
