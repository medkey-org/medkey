<?php

use app\common\wrappers\Block;
use app\modules\security\widgets\grid\AclRoleGrid;
use app\modules\security\assets\SecurityAsset;
use app\modules\security\SecurityModule;

$this->title = \Yii::t('app', 'Roles');
$this->params['breadcrumbs'][] = \Yii::t('app', 'Roles');

SecurityAsset::register($this);
?>

<?= AclRoleGrid::widget([
    'wrapper' => true,
    'wrapperOptions' => [
        'header' => SecurityModule::t('common', 'List of roles'),
        'wrapperClass' => Block::className()
    ],
]);