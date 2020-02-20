<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\common\helpers\Html;
use app\common\widgets\Nav;
use yii\bootstrap\NavBar;
use app\common\widgets\Breadcrumbs;
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
    <link href="/bundles/style.bundle.css" rel="stylesheet">
    <script type="text/javascript">
        'use strict';
        window.serverVars = <?= ClientHelper::getJsonParameters(); ?>
    </script>
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
    <?php
    if(!Yii::$app->user->getIsGuest()) {
        NavBar::begin([
            'brandLabel' => \Yii::t('app', \Yii::$container->get(\app\modules\config\application\ConfigServiceInterface::class)->getApplicationTitle()),
            'brandUrl' => Yii::$app->homeUrl,
            'innerContainerOptions' => [
                'class' => 'container-fluid',
            ],
            'options' => [
                'class' => 'navbar-inverse navbar-fixed-top',
            ],
        ]);
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => [
                [
                    'label' => \Yii::t('app', 'Patients'),
                    'url' => ['/medical/ui/patient/index'],
                    'visible' => \Yii::$container->get(\app\modules\medical\application\PatientServiceInterface::class)->isAllowed('getPatientList'),
                ],
                [
                    'label' => \Yii::t('app', 'EHR'),
                    'url' => ['/medical/ui/ehr/index'],
                    'visible' => \Yii::$container->get(\app\modules\medical\application\EhrServiceInterface::class)->isAllowed('getEhrList'),
                ],
                [
                    'label' => \Yii::t('app', 'Orders'),
                    'url' => ['/crm/ui/order/index'],
                    'visible' => \Yii::$container->get(\app\modules\crm\application\OrderServiceInterface::class)->isAllowed('getOrderList'),
                ],
//                [
//                    'label' => \Yii::t('app', 'Medworker Workplace'),
//                    'url' => ['/medical/ui/medworker-workplace/index'],
//                ],
                [
                    'label' => \Yii::t('app', 'Referrals'),
                    'url' => ['/medical/ui/referral/index'],
                    'visible' => \Yii::$container->get(\app\modules\medical\application\ReferralServiceInterface::class)->isAllowed('getReferralList'),
                ],
                [
                    'label' => \Yii::t('app', 'Attendances'),
                    'url' => ['/medical/ui/attendance/index'],
                    'visible' => \Yii::$container->get(\app\modules\medical\application\AttendanceServiceInterface::class)->isAllowed('getAttendanceList'),
                ],
                [
                    'label' => \Yii::t('app', 'Security'),
                    'visible' =>
                        \Yii::$container->get(\app\modules\security\application\UserServiceInterface::class)->isAllowed('getUserList')
                        || \Yii::$container->get(\app\modules\security\application\AclServiceInterface::class)->isAllowed('getAclRoleList')
                        || \Yii::$container->get(\app\modules\security\application\AclServiceInterface::class)->isAllowed('getAclList'),
                    'items' => [
                        [
                            'label' => \Yii::t('app', 'Users'),
                            'url' => ['/security/ui/user/index'],
                            'visible' => \Yii::$container->get(\app\modules\security\application\UserServiceInterface::class)->isAllowed('getUserList'),
                        ],
                        [
                            'label' => \Yii::t('app', 'Employees'),
                            'url' => ['/organization/ui/employee/index'],
                            'visible' => \Yii::$container->get(\app\modules\security\application\UserServiceInterface::class)->isAllowed('getUserList'),
                        ],
                        [
                            'label' => \Yii::t('app', 'Roles'),
                            'url' => ['/security/ui/acl-role/index'],
                            'visible' => \Yii::$container->get(\app\modules\security\application\AclServiceInterface::class)->isAllowed('getAclRoleList'),
                        ],
                        [
                            'label' => \Yii::t('app', 'Access control list'),
                            'url' => ['/security/ui/acl/index'],
                            'visible' => \Yii::$container->get(\app\modules\security\application\AclServiceInterface::class)->isAllowed('getAclList'),
                        ],
                    ],
                ],
                [
                    'label' => \Yii::t('app', 'Setting'),
                    'visible' =>
                        \Yii::$container->get(\app\modules\config\application\DirectoryServiceInterface::class)->isAllowed('getDirectoryList')
                        || \Yii::$container->get(\app\modules\organization\application\EmployeeServiceInterface::class)->isAllowed('getEmployeeList')
                        || \Yii::$container->get(\app\modules\workplan\application\WorkplanServiceInterface::class)->isAllowed('getWorkplanList')
                        || \Yii::$container->get(\app\modules\location\application\LocationServiceInterface::class)->isAllowed('getLocationList'),
                    'items' => [
                        [
                            'label' => \Yii::t('app', 'Common settings'),
                            'url' => ['/config/ui/setting/index'],
                            'visible' => \Yii::$container->get(\app\modules\config\application\ConfigServiceInterface::class)->isAllowed('getAllSettings'),
                        ],
                        [
                            'label' => \Yii::t('app', 'Directories'),
                            'url' => ['/config/ui/directory/index'],
                            'visible' => \Yii::$container->get(\app\modules\config\application\DirectoryServiceInterface::class)->isAllowed('getDirectoryList'),
                        ],
                        [
                            'label' => \Yii::t('app', 'Workflow builder'),
                            'url' => ['/config/ui/workflow/index'],
                            'visible' => \Yii::$container->get(\app\modules\config\application\WorkflowServiceInterface::class)->isAllowed('getWorkflowList'),
                        ],
                        [
                            'label' => \Yii::t('app', 'Workflow statuses'),
                            'url' => ['/config/ui/workflow-status/index'],
                            'visible' => \Yii::$container->get(\app\modules\config\application\WorkflowStatusServiceInterface::class)->isAllowed('getWorkflowStatusList'),
                        ],
                        [
                            'label' => \Yii::t('app', 'Workplan'),
                            'url' => ['/workplan/ui/workplan/index'],
                            'visible' => \Yii::$container->get(\app\modules\workplan\application\WorkplanServiceInterface::class)->isAllowed('getWorkplanList'),
                        ],
                        [
                            'label' => \Yii::t('app', 'Locations'),
                            'url' => ['/location/ui/location/index'],
                            'visible' => \Yii::$container->get(\app\modules\location\application\LocationServiceInterface::class)->isAllowed('getLocationList'),
                        ],
                        [
                            'label' => \Yii::t('app', 'Service price list'),
                            'url' => ['/medical/ui/service-price-list/index'],
                            'visible' => \Yii::$container->get(\app\modules\location\application\LocationServiceInterface::class)->isAllowed('getLocationList'),
                        ],
                    ],
                ],
                [
                    'label' => \Yii::t('app', 'Profile'),
                    'items' => [
                        [
                            'label' => \Yii::t('app', 'Help'),
                            'url' => ['/help/ui/help'],
                        ],
                        (
                            '<li>'
                            . '<a id = "layout-exit" href=' . \app\common\helpers\Url::to(['/security/ui/user/logout'])
                            . ' onclick="' . \app\common\widgets\RegisterModal::createMethod('confirm', \app\modules\security\SecurityModule::t('user', 'Logout from system?')) . '">'
                            . \Yii::t('app', 'Logout')
                            . ' (' . \Yii::$app->user->identity->login . ')</a>'
                            . '</li>'
                        ),
                    ],
                ],
//                (
//                    '<li>'
//                    . '<a id = "layout-exit" href=' . \app\common\helpers\Url::to(['/security/ui/user/logout']) . ' onclick="' . \app\common\widgets\RegisterModal::createMethod('confirm', 'Вы уверены, что хотите выйти из системы?') . '">Выйти (' . \Yii::$app->user->identity->login . ')</a>'
//                    . '</li>'
//                ),
            ]
        ]);
        NavBar::end();
    }
    ?>
    <div class="container-fluid">
        <?= \Yii::$app->user->getIsGuest() ? '' : Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>
<?php
//\yii\widgets\Pjax::end();
?>
<footer class="footer">
    <div class="container-fluid">
        <p class="pull-left">&copy; <a href="https://application.org?refsrc=appfoot&appver=<?=\Yii::$app->version;?>"><?= \Yii::t('app', 'Medkey'); ?></a> <?= date('Y') ?>, <?= \Yii::t('app', 'version'); ?> <?= \Yii::$app->version; ?></p>
        <p class="pull-right">Licensed under <a href="https://github.com/medkey-org/medkey/blob/master/LICENSE">GPL v3.0</a></p>
    </div>
</footer>
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