<?php
/** @var $model \app\modules\medical\models\orm\Referral */

use app\modules\medical\MedicalModule;
use app\modules\medical\widgets\card\ReferralCard;

$this->title = MedicalModule::t('referral', 'Referral card');
$this->params['breadcrumbs'][] = ['url' => '/medical/ui/referral/index', 'label' => MedicalModule::t('referral', 'Referral registry')];
$this->params['breadcrumbs'][] = MedicalModule::t('referral', 'Referral card');

?>

<?= ReferralCard::widget([
    'model' => $model,
]); ?>