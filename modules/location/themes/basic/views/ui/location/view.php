<?php

use app\modules\location\widgets\card\LocationCard;
use app\modules\location\LocationModule;

$this->title = LocationModule::t('common', 'Medkey');
$this->params['breadcrumbs'][] = ['url' => '/location/ui/location/index', 'label' => LocationModule::t('common', 'Locations')];
$this->params['breadcrumbs'][] = LocationModule::t('common', 'Location\'s card');

?>

<?= LocationCard::widget([
    'model' => $model->id,
]); ?>