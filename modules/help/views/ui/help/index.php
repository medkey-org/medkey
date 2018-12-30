<?php

use app\modules\help\HelpModule;
use yii\helpers\Url;

$this->title = 'Help';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="b-block b-block__backdrop">

<h4>Medkey Healthcare System v<?= \Yii::$app->version; ?></h4>

<p>Resources:</p>

<ul>
    <li><a href="http://www.medkey.org?refsrc=apphelpindex&appver=<?=\Yii::$app->version;?>"><?= HelpModule::t('app','Website with detailed information');?></a></li>
</ul>

<p>Additional installation information:</p>

<ul>
    <li><a href="<?= Url::toRoute('/help/ui/service/instance'); ?>"><?= HelpModule::t('app', 'Instance technical information');?></a></li>
    <li><a href="<?= Url::toRoute('/help/ui/service/statistics'); ?>"><?= HelpModule::t('app', 'Instance statistics');?></a></li>
</ul>

<p>Enabled modules:</p>

<ul>
    <?php foreach (\Yii::$app->modules as $key=>$module) {
        echo '<li>' . $key . '</li>';
    } ?>
</ul>

</div>