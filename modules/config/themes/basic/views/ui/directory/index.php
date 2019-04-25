<?php

use app\modules\config\assets\ConfigAsset;
use app\modules\config\widgets\grid\DirectoryGrid;
use app\modules\config\ConfigModule;

$this->title = ConfigModule::t('common', 'Directories');
$this->params['breadcrumbs'][] = $this->title;

ConfigAsset::register($this);
?>

<?= DirectoryGrid::widget(); ?>