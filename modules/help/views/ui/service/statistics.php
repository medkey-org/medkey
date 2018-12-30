<?php

use app\modules\help\HelpModule;
use yii\helpers\Url;

$this->title = HelpModule::t('app', 'Instance statistics');
$this->params['breadcrumbs'][] = ['url' => '/help/ui/help', 'label' => HelpModule::t('app', 'Help')];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="b-block b-block__backdrop">

<h4>Statistics</h4>

</div>