<?php
namespace app\modules\crm\widgets\form;

use app\common\db\ActiveRecord;
use app\common\helpers\Html;
use app\common\widgets\FormWidget;
use app\common\wrappers\DynamicModal;
use app\modules\config\entities\CurrencyEntity;
use app\modules\crm\application\OrderServiceInterface;
use app\modules\crm\CrmModule;
use app\modules\crm\models\orm\Order;
use app\modules\crm\models\form\OrderItem;
use app\modules\medical\models\orm\Service;

/**
 * Class OrderItemCreateForm
 * @package Module\CRM
 * @copyright 2012-2019 Medkey
 */
class OrderItemCreateForm extends FormWidget
{
    /**
     * @var OrderItem
     */
    public $model;
    /**
     * @var Order
     */
    public $orderId;
    /**
     * @var array
     */
    public $action = ['/crm/rest/order-item/create'];
    /**
     * @var array
     */
    public $validationUrl = ['/crm/rest/order-item/validate-create'];
    /**
     * @var OrderServiceInterface
     */
    public $orderService;

    /**
     * OrderItemCreateForm constructor.
     * @param OrderServiceInterface $orderService
     * @param array $config
     */
    public function __construct(OrderServiceInterface $orderService, array $config = [])
    {
        $this->orderService = $orderService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->model = $this->orderService->getOrderItemForm($this->model);
//        $this->model = OrderItem::ensureWeak($this->model, ActiveRecord::SCENARIO_CREATE);
        $this->model->order_id = $this->orderId;
        parent::init();
    }

    public function renderForm($model, $form)
    {
        echo $form->field($model, 'order_id')
            ->hiddenInput();
        echo $form->field($model, 'service_id')
            ->select2(Service::listAll());
//        echo $form->field($model, 'currency')
//            ->select2(CurrencyEntity::currencyListData());
//        echo $form->field($model, 'currency_sum_per_unit')
//            ->textInput([
//                'disabled' => true,
//            ]);
        echo $form->field($model, 'currency_sum')
            ->moneyInput();
        echo $form->field($model, 'final_currency_sum')
            ->moneyInput();
//        echo $form->field($model, 'discount_point')
//            ->moneyInput();
//        echo $form->field($model, 'discount_currency_sum')
//            ->moneyInput();
//        echo $form->field($model, 'qty');
        echo Html::submitButton(\Yii::t('app', 'Save'), [
            'class' => 'btn btn-primary',
            'icon' => 'save'
        ]);
        echo '&nbsp';
        echo Html::button(\Yii::t('app', 'Cancel'), [
            'class' => 'btn btn-default',
            'data-dismiss' => 'modal'
        ]);

    }

    /**
     * @inheritdoc
     */
    public function wrapperOptions()
    {
        return [
            'wrapperClass' => DynamicModal::class,
            'header' => CrmModule::t('order', 'Create order position'),
        ];
    }
}
