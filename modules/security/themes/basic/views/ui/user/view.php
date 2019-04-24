<?php

/* @var $this \app\common\web\View */
/* @var $model \app\modules\security\models\orm\User */

use app\modules\security\widgets\card\UserCard;
use app\modules\security\assets\SecurityAsset;
use app\common\wrappers\Block;
use app\modules\security\SecurityModule;

$this->title = \Yii::t('app', 'User');
$this->params['breadcrumbs'][] = ['url' => '/security/ui/user/index', 'label' => \Yii::t('app', 'Users')];
$this->params['breadcrumbs'][] = \Yii::t('app', 'User') . ' #' . $model;

SecurityAsset::register($this);
?>

<?= UserCard::widget([
    'model' => $model,
    'wrapper' => true,
    'wrapperOptions' => [
        'wrapperClass' => Block::class,
        'header' => SecurityModule::t('user', 'User'),
    ]
]); ?>