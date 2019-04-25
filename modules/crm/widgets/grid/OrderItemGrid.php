<?php
namespace app\modules\crm\widgets\grid;

use app\common\button\WidgetLoaderButton;
use app\common\grid\GridView;
use app\common\wrappers\DynamicModal;
use app\modules\config\entities\CurrencyEntity;
use app\modules\crm\application\OrderServiceInterface;
use app\modules\crm\CrmModule;
use app\modules\crm\models\finders\OrderItemFinder;
use app\modules\crm\models\orm\OrderItem;
use app\modules\crm\widgets\form\OrderItemCreateForm;
use app\modules\crm\widgets\form\OrderItemUpdateForm;
use app\modules\medical\models\orm\Service;

/**
 * Class OrderItemGrid
 * @package Module\CRM
 * @copyright 2012-2019 Medkey
 */
class OrderItemGrid extends GridView
{
    /**
     * @var OrderItem
     */
    public $filterModel;
    /**
     * @var string
     */
    public $orderId;
    /**
     * @var OrderServiceInterface
     */
    public $orderService;
    public $cardId;

    /**
     * OrderItemGrid constructor.
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
        $this->filterModel = OrderItemFinder::ensure($this->filterModel, 'search');
        $this->filterModel->orderId = $this->orderId;
        $this->dataProvider = $this->orderService->getOrderItemList($this->filterModel);
        $order = $this->orderService->getOrderById($this->orderId);
        $this->filterModel->orderId = $this->orderId;
        $this->columns = [
            [
                'attribute' => 'service_id',
                'value' => function (OrderItem $model) {
                    if (isset($model->service) && $model->service instanceof Service) {
                        return $model->service->title;
                    }
                }
            ],
            [
                'attribute' => 'currency_sum',
                'value' => function ($model) {
                    $currency = CurrencyEntity::findCurrency($model->currency);
                    $cur = $currency;
                    if (empty($model->currency_sum)) {
                        $model->currency_sum = '0.00';
                    }
                    return $model->currency_sum . ' ' . $cur;
                }
            ],
            [
                'attribute' => 'final_currency_sum',
                'value' => function ($model) {
                    $currency = CurrencyEntity::findCurrency($model->currency);
                    $cur = $currency;
                    if (empty($model->final_currency_sum)) {
                        $model->final_currency_sum = '0.00';
                    }
                    return $model->final_currency_sum . ' ' . $cur;
                }
            ],
        ];
        if ($order->isNew()) {
            $this->actionButtons['create'] = [
                'class' => WidgetLoaderButton::class,
                'widgetClass' => OrderItemCreateForm::class,
                'widgetConfig' => [
                    'orderId' => $this->orderId,
                    'afterUpdateBlockId' => $this->cardId,
                    'wrapperOptions' => [
                        'wrapperClass' => DynamicModal::class,
                        'header' => CrmModule::t('order', 'Create order position'),
                    ],
                ],
                'disabled' => false,
                'isDynamicModel' => false,
                'value' => '',
                'options' => [
                    'class' => 'btn btn-primary btn-xs',
                    'icon' => 'plus',
                ],
            ];
            $this->actionButtons['update'] = [
                'class' => WidgetLoaderButton::class,
                'widgetClass' => OrderItemUpdateForm::class,
                'widgetConfig' => [
                    'afterUpdateBlockId' => $this->cardId,
                    'wrapperOptions' => [
                        'wrapperClass' => DynamicModal::class,
                        'header' => CrmModule::t('order', 'Update order position'),
                    ],
                ],
                'disabled' => true,
                'isDynamicModel' => true,
                'value' => '',
                'options' => [
                    'class' => 'btn btn-primary btn-xs',
                    'icon' => 'edit',
                ],
            ];
        }
        parent::init();
    }
}