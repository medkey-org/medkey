<?php

use app\common\wrappers\Block;
use app\modules\security\widgets\grid\UserGrid;
use app\modules\security\assets\SecurityAsset;
use app\modules\security\SecurityModule;

$this->title = \Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = \Yii::t('app', 'Users');

SecurityAsset::register($this);
?>

<?= UserGrid::widget([
    'wrapper' => true,
    'wrapperOptions' => [
        'header' => SecurityModule::t('common', 'List of users'),
        'wrapperClass' => Block::className()
    ],
]);