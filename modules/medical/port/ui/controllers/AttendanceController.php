<?php
namespace app\modules\medical\port\ui\controllers;

use app\common\web\Controller;

/**
 * Class AttendanceController
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class AttendanceController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionView($id = null)
    {
        return $this->render('view', [
            'model' => $id,
        ]);
    }
}
