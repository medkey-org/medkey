<?php

use app\modules\config\assets\ConfigAsset;
use app\modules\config\widgets\grid\DocumentTemplateGrid;
use app\modules\config\ConfigModule;

$this->title = ConfigModule::t('common', 'Document templates');
$this->params['breadcrumbs'][] = $this->title;

ConfigAsset::register($this);
?>

<?= DocumentTemplateGrid::widget(); ?>