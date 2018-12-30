<?php

use app\modules\security\widgets\form\UserLoginForm;
use app\modules\security\assets\SecurityAsset;
use app\common\helpers\Html;

$this->title = getenv('COMMON_WEB_TITLE');

SecurityAsset::register($this);
?>

<?php
echo Html::beginDiv([
    'class' => 'login-form-back'
]);
echo Html::beginDiv([
    'class' => 'login-form-div-center col-md-4 col-xs-12 col-lg-3 col-sm-6'
]);
echo Html::beginDiv([
    'class' => 'login-form-content',
]);
echo Html::beginDiv([
    'class' => 'panel panel-default',
]);
echo Html::beginDiv([
    'class' => 'panel-heading',
]);
echo '<div style="text-align: center; font-size: 18px;">' . getenv('COMMON_WEB_TITLE') . '</div>';
echo Html::endDiv();
echo Html::beginDiv([
    'class' => 'panel-body',
]);
?>
<?= UserLoginForm::widget(); ?>
<?php
echo Html::endDiv();
echo Html::endDiv();
echo Html::endDiv();
echo Html::endDiv();
echo Html::endDiv();
?>