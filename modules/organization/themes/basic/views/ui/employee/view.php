<?php

/* @var $this \app\common\web\View */

use app\modules\organization\widgets\card\EmployeeCard;
use app\modules\organization\OrganizationModule;

$this->title = 'Сотрудник';
$this->params['breadcrumbs'][] = ['url' => '/security/ui/user/index', 'label' => \Yii::t('app', 'Users')];
$this->params['breadcrumbs'][] = ['url' => ['/security/ui/user/view', 'id' => $userId], 'label' => \Yii::t('app', 'User') . ' #' . $userId];
$this->params['breadcrumbs'][] = OrganizationModule::t('common', 'Employee');

?>

<?= EmployeeCard::widget([
    'model' => $modelId,
    'userId' => $userId,
]); ?>