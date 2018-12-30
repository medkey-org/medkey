<?php
namespace app\modules\config\port\ui\controllers;

use app\common\web\Controller;

/**
 * Class WorkflowStatusController
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class WorkflowStatusController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
