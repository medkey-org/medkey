<?php
namespace app\modules\crm\port\ui\controllers;

use app\common\web\Controller;

/**
 * Class OrderItemController
 * @package Module\CRM
 * @copyright 2012-2019 Medkey
 * @deprecated
 */
class OrderItemController extends Controller
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @param string|int $id
     * @return string
     */
    public function actionView($id = null)
    {
        $model = $id;
        return $this->render('view', compact('model'));
    }
}
