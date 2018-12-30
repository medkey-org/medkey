<?php

use app\modules\dashboard\port\ui\assets\DashboardAsset;
use app\modules\dashboard\widgets\misc\UserDashboardTab;

$this->title = 'Мои рабочие столы';

DashboardAsset::register($this);

echo UserDashboardTab::widget();
