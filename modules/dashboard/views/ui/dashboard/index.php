<?php

use app\modules\dashboard\port\ui\assets\DashboardAsset;
use app\modules\dashboard\widgets\misc\UserDashboardTab;
use app\common\web\View;

$this->title = \app\modules\dashboard\DashboardModule::t('dashboard', 'My dashboards');

DashboardAsset::register($this);

echo UserDashboardTab::widget();

?>

<!-- The toolbar will be rendered in this container. -->
<div id="toolbar-container"></div>

<!-- This container will become the editable. -->
<div id="editor">
    <p>This is the initial editor content.</p>
</div>

<?php $this->registerJsFile(\Yii::getRootAlias('web') . DIRECTORY_SEPARATOR . 'bundles/bundle_document.js'); ?>

<?php echo \Yii::$app->view->registerJs(<<<JS
        registerDocumentEdit();
JS
, View::POS_END);