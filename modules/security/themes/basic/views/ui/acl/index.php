<?php

/** @var int $type */

use app\common\wrappers\Block;
use app\modules\security\widgets\grid\AclGrid;
use app\modules\security\assets\SecurityAsset;
use app\modules\security\SecurityModule;

$this->title = 'ACL';
$this->params['breadcrumbs'][] = \Yii::t('app', 'Access control list');

SecurityAsset::register($this);
?>

<?= AclGrid::widget([
    'wrapper' => true,
    'wrapperOptions' => [
        'header' => SecurityModule::t('common', 'Access control list'),
        'wrapperClass' => Block::className()
    ],
]);