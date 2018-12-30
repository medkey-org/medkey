<?php

$overridden = [
    \yii\helpers\Html::class,
];

foreach ($overridden as $className) {
    Yii::$classMap[$className] = '@app/override/' . str_replace('\\', '/', $className) . '.php';
}