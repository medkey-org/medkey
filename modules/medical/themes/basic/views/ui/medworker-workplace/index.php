<?php
use app\modules\medical\assets\ScheduleMedworkerAsset;
$this->title = 'Medkey';
$this->params['breadcrumbs'][] = 'АРМ Врача';
ScheduleMedworkerAsset::register($this);
?>
<div class="b-block b-block__backdrop">
    <div id="app-workplace"></div>
</div>