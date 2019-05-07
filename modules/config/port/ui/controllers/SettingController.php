<?php
namespace app\modules\config\port\ui\controllers;

use app\common\web\Controller;

class SettingController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
