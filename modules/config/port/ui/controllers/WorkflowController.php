<?php
namespace app\modules\config\port\ui\controllers;

use app\common\web\Controller;

/**
 * Class WorkflowController
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class WorkflowController extends Controller
{
    /**
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @param null $id
     * @return mixed
     */
    public function actionView($id = null)
    {
        return $this->render('view', ['workflowId' => $id]);
    }
}
