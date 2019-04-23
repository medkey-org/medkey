<?php

use app\modules\security\assets\SecurityAsset;
use app\modules\medical\widgets\grid\ReferralGrid;
use app\modules\medical\MedicalModule;
use app\common\wrappers\Block;

$this->title = MedicalModule::t('referral', 'Referral registry');
$this->params['breadcrumbs'][] = $this->title;

?>

<?= ReferralGrid::widget([
    'wrapper' => true,
    'wrapperOptions' => [
        'header' => $this->title,
        'wrapperClass' => Block::class,
    ],
]);