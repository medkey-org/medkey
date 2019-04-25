<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\common\helpers\Html;
use app\assets\AppAsset;
use app\common\helpers\ClientHelper;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= \Yii::$app->language ?>">
<head>
    <meta charset="<?= \Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <script type="text/javascript">
        'use strict';
        window.serverVars = <?= ClientHelper::getJsonParameters(); ?>
    </script>
    <link href="/bundles/style.bundle.css" rel="stylesheet">
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?php
//\yii\widgets\Pjax::begin([
//    'timeout' => 3000,
//    'id' => \app\common\base\UniqueKey::generate('pjax')
//]);
?>
<div class="wrap">

    }
    ?>
        <?= $content ?>
</div>
<?php
//\yii\widgets\Pjax::end();
?>
<?php $this->registerJsFile(\Yii::getRootAlias('web') . DIRECTORY_SEPARATOR . 'bundles/app-bundle.js'); ?>
<?php $this->registerJsFile(\Yii::getRootAlias('web') . DIRECTORY_SEPARATOR . 'bundles/bundle.js'); ?>
<?php $this->endBody() ?>
<script>
    $('#layout-exit').on('click', function (e) { // todo переработать RegisterModal
        var target = $(e.currentTarget);
        var href = target.attr('href');
        if (href) {
            window.location.href = href;
        } else {
            console.warn('Выход невозможен. Обратитесь к администратору.');
        }
    });
</script>
</body>
</html>
<?php $this->endPage() ?>