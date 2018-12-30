<?php

/** @var $model \app\modules\medical\models\orm\Referral */

use app\modules\medical\MedicalModule;
use app\modules\medical\widgets\card\EhrCard;

$this->title = MedicalModule::t('common', 'Medkey');
$this->params['breadcrumbs'][] = ['url' => '/medical/ui/ehr/index', 'label' => 'Список медицинских карт'];
$this->params['breadcrumbs'][] = 'Карточка медицинской карты';

?>

<?= EhrCard::widget([
    'model' => $model,
]); ?>