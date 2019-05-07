<?php
namespace app\modules\crm\port\rest\controllers;

use app\common\web\Controller;
use app\common\widgets\ActiveForm;
use app\modules\crm\application\OrderServiceInterface;
use app\modules\crm\models\form\OrderItem as OrderItemForm;
use yii\base\Module;

/**
 * Class OrderItemController
 * @package Module\CRM
 * @copyright 2012-2019 Medkey
 */
class OrderItemController extends Controller
{
    public $orderService;

    public function __construct($id, Module $module, OrderServiceInterface $orderService, array $config = [])
    {
        $this->orderService = $orderService;
        parent::__construct($id, $module, $config);
    }

    public function actionCreate()
    {
        $form = new OrderItemForm([
            'scenario' => 'create',
        ]);
        $form->load(\Yii::$app->request->getBodyParams());
        return $this->asJson($this->orderService->createOrderItem($form));
    }

    public function actionValidateCreate()
    {
        $form = new OrderItemForm([
            'scenario' => 'create'
        ]);
        $form->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson(ActiveForm::validate($form));
    }

    public function actionUpdate($id)
    {
        $form = new OrderItemForm([
            'scenario' => 'update',
        ]);
        $form->load(\Yii::$app->request->getBodyParams());
        return $this->asJson($this->orderService->updateOrderItem($id, $form));
    }

    public function actionValidateUpdate()
    {
        $form = new OrderItemForm([
            'scenario' => 'create'
        ]);
        $form->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson(ActiveForm::validate($form));
    }
}
