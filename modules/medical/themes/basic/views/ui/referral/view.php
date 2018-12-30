<?php
/** @var $model \app\modules\medical\models\orm\Referral */

use app\modules\medical\MedicalModule;
use app\modules\medical\widgets\card\ReferralCard;

$this->title = MedicalModule::t('common', 'Medkey');
$this->params['breadcrumbs'][] = ['url' => '/medical/ui/referral/index', 'label' => 'Список направлений'];
$this->params['breadcrumbs'][] = 'Карточка направления';

?>

<?= ReferralCard::widget([
    'model' => $model,
]); ?>