<?php

use app\modules\config\assets\ConfigAsset;
use app\modules\config\widgets\grid\DirectoryGrid;
use app\modules\config\ConfigModule;

$this->title = 'Справочники';
$this->params['breadcrumbs'][] = ConfigModule::t('common', 'Directories');

ConfigAsset::register($this);
?>

<?= DirectoryGrid::widget(); ?>