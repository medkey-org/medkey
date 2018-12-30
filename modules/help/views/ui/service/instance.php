<?php

use app\modules\help\HelpModule;
use yii\helpers\Url;

$this->title = HelpModule::t('app', 'Instance information');
$this->params['breadcrumbs'][] = ['url' => '/help/ui/help', 'label' => HelpModule::t('app', 'Help')];
$this->params['breadcrumbs'][] = $this->title;

?>


<div class="b-block b-block__backdrop">

<h4>Environment</h4>
<p>PHP version: <?= phpversion(); ?></p>
<p>Host OS: <?= php_uname(); ?></p>

</div>