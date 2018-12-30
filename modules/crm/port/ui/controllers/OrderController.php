<?php
namespace app\modules\crm\port\ui\controllers;

use app\common\base\Module;
use app\common\db\ActiveRecord;
use app\common\web\CrudController;
use app\modules\crm\application\OrderService;


/**
 * Class OrderController
 * @package Module\CRM
 * @copyright 2012-2019 Medkey
 */
class OrderController extends CrudController
{
    public function actionView($id = null)
    {
        $modelClass = $this->modelClass;
        /** @var $modelClass ActiveRecord */
        $model = $modelClass::ensureWeak($id);
        empty(\Yii::$app->request->get('ehrId')) ?: $model->ehr_id = \Yii::$app->request->get('ehrId');
        return $this->render('view', [
            'model' => $model,
        ]);
    }
}
