<?php
namespace app\modules\crm\widgets\grid;

use app\common\button\LinkActionButton;
use app\common\db\ActiveRecord;
use app\common\grid\GridView;
use app\common\helpers\Html;
use app\common\helpers\Url;
use app\modules\config\entities\CurrencyEntity;
use app\modules\crm\application\OrderServiceInterface;
use app\modules\location\models\orm\Location;
use app\modules\crm\models\finders\OrderFinder;
use app\modules\crm\models\orm\Order;
use app\modules\medical\models\orm\Ehr;
use yii\web\JsExpression;

/**
 * Class OrderGrid
 * @package Module\CRM
 * @copyright 2012-2019 Medkey
 */
class OrderGrid extends GridView
{
    /**
     * @var bool
     */
    public $visibleFilterRow = true;
    /**
     * @var OrderFinder
     */
    public $filterModel;
    /**
     * @var Ehr
     */
    public $ehr;
    /**
     * @var OrderServiceInterface
     */
    public $orderService;


    /**
     * OrderGrid constructor.
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
        $this->filterModel = OrderFinder::ensure($this->filterModel, 'search', $this->formData);
        $this->ehr = Ehr::ensureWeak($this->ehr);
        $this->filterModel->ehrId = $this->ehr->id;
        $this->dataProvider = $this->orderService->getOrderList($this->filterModel);
        $this->actionButtons['create'] = [
            'class' => LinkActionButton::class,
            'url' => ['/crm/ui/order/view', 'scenario' => ActiveRecord::SCENARIO_CREATE, 'ehrId' => $this->ehr->id], // todo херня с передачей ehr
            'isDynamicModel' => false,
            'isAjax' => false,
            'disabled' => false,
            'value' => '',
            'options' => [
                'class' => 'btn btn-primary btn-xs',
                'icon' => 'plus',
            ],
        ];
//        $this->actionButtons['delete'] = [
//            'class' => LinkActionButton::class,
//            'url' => ['/crm/ui/order/delete'],
//            'isDynamicModel' => true,
//            'isAjax' => true,
//            'disabled' => true,
//            'isConfirm' => true,
//            'afterUpdateBlock' => $this,
//            'value' => '',
//            'options' => [
//                'class' => 'btn btn-danger btn-xs',
//                'icon' => 'remove',
//            ],
//        ];
        $this->columns = [
            [
                'attribute' => 'number',
                'value' => function(Order $model) {
                    return Html::a(Html::encode($model->number), Url::to(['/crm/ui/order/view', 'id' => $model->id]));
                },
                'format' => 'html',
                'options' => [
                    'class' => 'col-xs-2 col-md-2',
                ],
            ],
            [
                'attribute' => 'status',
                'value' => function (Order $model) {
                    return $model->getStatusName();
                },
                'filter' => function () {
                    return Html::activeSelect2Input(
                        $this->filterModel,
                        'status',
                        Order::statuses(),
                        [
                            'class' => 'form-control',
                            'empty' => true,
                            'placeholder' => \Yii::t('app', 'Select value...'),
                        ],
                        [
                            'allowClear' => true,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return 'Ничего не найдено.'; }"),
                            ],
                        ]
                    );
                },
                'options' => [
                    'class' => 'col-xs-2'
                ],
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
                },
                'filter' => function () {
                    return Html::activeMoneyInput($this->filterModel, 'currencySum', ['class' => 'form-control']);
                },
                'options' => [
                    'class' => 'col-xs-2'
                ],
            ],
            [
                'attribute' => 'location_id',
                'value' => function (Order $model) {
                    $location = $model->location;
                    if (isset($location) && ($location instanceof Location)) {
                        return $location->code;
                    }
                },
                'filter' => function () {
                    return Html::activeTextInput($this->filterModel, 'locationCode', ['class' => 'form-control']);
                },
                'options' => [
                    'class' => 'col-xs-2'
                ],
            ],
        ];
        parent::init();
    }
}
