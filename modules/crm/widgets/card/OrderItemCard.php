<?php
namespace app\modules\crm\widgets\card;

use app\common\card\CardView;
use app\common\helpers\ArrayHelper;
use app\common\helpers\Html;
use app\common\helpers\Url;
use app\common\widgets\ActiveForm;
use app\common\wrappers\Block;
use app\modules\config\entities\CurrencyEntity;
use app\modules\loyalty\entities\Point;
use app\modules\crm\models\orm\Order;
use app\modules\crm\models\orm\OrderItem;
use app\modules\crm\OrderModule;
use yii\web\JsExpression;

/**
 * Class OrderItemCard
 * @package Module\CRM
 * @copyright 2012-2019 Medkey
 * @deprecated
 */
class OrderItemCard extends CardView
{
    /**
     * @var OrderItem
     */
    public $model;
    /**
     * @var Order
     */
    public $order;
    /**
     * @var bool
     */
    public $wrapper = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->order = Order::ensureWeak($this->order);
        empty($this->order->id) ?: $this->model->order_id = $this->order->id;
        $this->formOptions = array_merge($this->formOptions, [
            'action' => Url::to(['/order/ui/order-item/' . $this->model->scenario, 'id' => $this->model->id]),
            'validationUrl' => Url::to(['/order/ui/order-item/validate-' . $this->model->scenario, 'id' => $this->model->id]),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function dataGroups()
    {
        return [
            'main' => [
                'title' => OrderModule::t('common', 'Order item\'s data'),
                'items' => [
                    [
                        'items' => [
                            [
                                'attribute' => 'type_transaction',
                                'scenarios' => [
                                    'create' => [
                                        'value' => function (OrderItem $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'type_transaction')
                                                ->select2(OrderItem::typeListData(),
                                                    ['multiple' => false],
                                                    ['custom' => false])->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (OrderItem $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'type_transaction')
                                                ->select2(OrderItem::typeListData(),
                                                    ['multiple' => false],
                                                    ['custom' => false])->label(false);
                                        }
                                    ],
                                    'default' => [
                                        'value' => function (OrderItem $model) {
                                            return $model->getTypeName();
                                        }
                                    ]
                                ],
                            ],
                            [
                                'attribute' => 'type_point',
                                'scenarios' => [
                                    'create' => [
                                        'value' => function (OrderItem $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'type_point')
                                                ->select2(Point::pointTypeListData())
                                                ->label(false);
                                        },
                                    ],
                                    'update' => [
                                        'value' => function (OrderItem $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'type_point')
                                                ->select2(Point::pointTypeListData())
                                                ->label(false);
                                        },
                                    ],
                                    'default' => [
                                        'value' => function (OrderItem $model) {
                                            return $model->getPointTypeName();
                                        }
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'items' => [
                            [
                                'attribute' => 'product_id',
                                'scenarios' => [
                                    'create' => [
                                        'value' => function (OrderItem $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'product_id')
                                                ->select2(ArrayHelper::map([$model->product], 'id', 'name'), [], [
                                                    'allowClear' => true,
                                                    'minimumInputLength' => 3,
                                                    'language' => [
                                                        'errorLoading' => new JsExpression("function () { return 'Ничего не найдено.'; }"),
                                                    ],
                                                    'ajax' => [
                                                        'url' => Url::to(['/product/rest/product/index']),
                                                        'dataType' => 'json',
                                                        'delay' => 1000,
                                                        'data' => new JsExpression('function (params) { return {q:params.term, page: params.page || 1}; }'),
                                                        'processResults' => new JsExpression('function (data, params) { return { results: data, pagination: {more: (params.page * 10) < data.count_filtered}}; }')
                                                    ],
                                                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                                    'templateResult' => new JsExpression('function (product) { if (product.loading) { return product.text; } else {return product.name; }}'),
                                                    'templateSelection' => new JsExpression('function (product) { return product.name || product.text; }'),
                                                ])->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (OrderItem $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'product_id')
                                                ->select2(ArrayHelper::map([$model->product], 'id', 'name'), [], [
                                                    'allowClear' => true,
                                                    'minimumInputLength' => 3,
                                                    'language' => [
                                                        'errorLoading' => new JsExpression("function () { return 'Ничего не найдено.'; }"),
                                                    ],
                                                    'ajax' => [
                                                        'url' => Url::to(['/product/rest/product/index']),
                                                        'dataType' => 'json',
                                                        'delay' => 1000,
                                                        'data' => new JsExpression('function (params) { return {q:params.term, page: params.page || 1}; }'),
                                                        'processResults' => new JsExpression('function (data, params) { return { results: data, pagination: {more: (params.page * 10) < data.count_filtered}}; }')
                                                    ],
                                                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                                    'templateResult' => new JsExpression('function (product) { if (product.loading) { return product.text; } else {return product.name; }}'),
                                                    'templateSelection' => new JsExpression('function (product) { return product.name || product.text; }'),
                                                ])->label(false);
                                        }
                                    ],
                                    'default' => [
                                        'value' => function (OrderItem $model) {
                                            if (isset($model->product)) {
                                                return $model->product->name;
                                            }

                                            return '';
                                        }
                                    ]
                                ],
                            ],
                            [
                                'attribute' => 'currency',
                                'scenarios' => [
                                    'create' => [
                                        'value' => function (OrderItem $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'currency')
                                                ->select2(CurrencyEntity::currencyListData())
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (OrderItem $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'currency')
                                                ->select2(CurrencyEntity::currencyListData())
                                                ->label(false);
                                        }
                                    ],
                                    'default' => [
                                        'value' => function (OrderItem $model) {
                                            $currency = CurrencyEntity::findCurrency($model->currency);
                                            return $currency;
                                        }
                                    ],
                                ]
                            ],
                        ],
                    ],
                    [
                        'items' => [
                            [
                                'attribute' => 'currency_sum_per_unit',
                                'scenarios' => [
                                    'create' => [
                                        'value' => function (OrderItem $model, ActiveForm $form) {
                                            $model->currency_sum_per_unit = '0.00';
                                            return $form->field($model, 'currency_sum_per_unit')
                                                ->moneyInput()
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (OrderItem $model, ActiveForm $form) {
                                            if (empty($model->currency_sum_per_unit)) {
                                                $model->currency_sum_per_unit = '0.00';
                                            }
//                                            $model->currency_sum_per_unit = CurrencyEntity::moneyDecode($model->currency_sum_per_unit, $model->currency);
                                            return $form->field($model, 'currency_sum_per_unit')
                                                ->moneyInput()
                                                ->label(false);
                                        }
                                    ],
                                    'default' => [
                                        'value' => function (OrderItem $model) {
                                            if (empty($model->currency_sum_per_unit)) {
                                                $model->currency_sum_per_unit = '0.00';
                                            }
                                            return $model->currency_sum_per_unit;
//                                            return CurrencyEntity::moneyDecode($model->currency_sum_per_unit, $model->currency);
                                        }
                                    ]
                                ],
                            ],
                            'qty',

                        ]
                    ],
                    [
                        'items' => [
                            [
                                'attribute' => 'currency_sum',
                                'scenarios' => [
                                    'create' => [
                                        'value' => function (OrderItem $model, ActiveForm $form) {
                                            $model->currency_sum = '0.00';
                                            return $form->field($model, 'currency_sum')
                                                ->moneyInput()
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (OrderItem $model, ActiveForm $form) {
                                            if (empty($model->currency_sum)) {
                                                $model->currency_sum = '0.00';
                                            }
//                                            $model->currency_sum = CurrencyEntity::moneyDecode($model->currency_sum, $model->currency);
                                            return $form->field($model, 'currency_sum')
                                                ->moneyInput()
                                                ->label(false);
                                        }
                                    ],
                                    'default' => [
                                        'value' => function (OrderItem $model) {
                                            if (empty($model->currency_sum)) {
                                                $model->currency_sum = '0.00';
                                            }
                                            return $model->currency_sum;
//                                            return CurrencyEntity::moneyDecode($model->currency_sum, $model->currency);
                                        }
                                    ],
                                ],
                            ],
                            [
                                'attribute' => 'point_sum',
                            ]
                        ],
                    ],
                    [
                        'items' => [
                            [
                                'attribute' => 'order_id',
                                'scenarios' => [
                                    'create' => [
                                        'label' => false,
                                        'value' => function (OrderItem $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'order_id')
                                                ->hiddenInput()
                                                ->label(false);
//                                                ->select2(ArrayHelper::map([$model->order], 'id', 'number'), [], [
//                                                    'allowClear' => true,
//                                                    'minimumInputLength' => 1,
//                                                    'language' => [
//                                                        'errorLoading' => new JsExpression("function () { return 'Ничего не найдено.'; }"),
//                                                    ],
//                                                    'ajax' => [
//                                                        'url' => Url::to(['/rest/order/index']),
//                                                        'dataType' => 'json',
//                                                        'delay' => 1000,
//                                                        'data' => new JsExpression('function (params) { return {q:params.term, page: params.page || 1}; }'),
//                                                        'processResults' => new JsExpression('function (data, params) { return { results: data, pagination: {more: (params.page * 10) < data.count_filtered}}; }')
//                                                    ],
//                                                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
//                                                    'templateResult' => new JsExpression('function (order) { if (order.loading) { return order.text; } else {return order.number; }}'),
//                                                    'templateSelection' => new JsExpression('function (order) { return order.number || order.text; }'),
//                                                ])->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'label' => false,
                                        'value' => function (OrderItem $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'order_id')
                                                ->hiddenInput()
                                                ->label(false);
//                                                ->select2(ArrayHelper::map([$model->order], 'id', 'number'), [], [
//                                                    'allowClear' => true,
//                                                    'minimumInputLength' => 1,
//                                                    'language' => [
//                                                        'errorLoading' => new JsExpression("function () { return 'Ничего не найдено.'; }"),
//                                                    ],
//                                                    'ajax' => [
//                                                        'url' => Url::to(['/rest/order/index']),
//                                                        'dataType' => 'json',
//                                                        'delay' => 1000,
//                                                        'data' => new JsExpression('function (params) { return {q:params.term, page: params.page || 1}; }'),
//                                                        'processResults' => new JsExpression('function (data, params) { return { results: data, pagination: {more: (params.page * 10) < data.count_filtered}}; }')
//                                                    ],
//                                                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
//                                                    'templateResult' => new JsExpression('function (order) { if (order.loading) { return order.text; } else {return order.number; }}'),
//                                                    'templateSelection' => new JsExpression('function (order) { return order.number || order.text; }'),
//                                                ])->label(false);
                                        }
                                    ],
                                    'default' => [
                                        'label' => false,
                                        'value' => false,
                                    ],
                                ],
                            ],
                        ]
                    ],
                    [
                        'items' => [
                            'base_accrual',
                            'base_redemption',
                        ]
                    ],
                ],
            ],
            'buttons' => [
                'title' => '',
                'showFrame' => false,
                'items' => [
                    [
                        'items' => [
                            [
                                'scenarios' => [
                                    'default' => [
                                        'value' => false,
                                        'label' => false,
                                    ],
                                    'update' => [
                                        'label' => false,
                                        'value' =>
                                            Html::submitButton(\Yii::t('app', 'Save'), [
                                                'class' => 'btn btn-primary',
                                                'icon'  => 'saved'
                                            ])
                                            . '&nbsp' . Html::button(\Yii::t('app', 'Cancel'), [
                                                'class' => 'btn btn-default',
                                                'data-card-switch' => 'default'
                                            ])
                                    ],
                                    'create' => [
                                        'label' => false,
                                        'value' => Html::submitButton(\Yii::t('app', 'Save'), [
                                                'class' => 'btn btn-primary',
                                                'icon'  => 'saved'
                                            ])
                                            . '&nbsp' . Html::button(\Yii::t('app', 'Cancel'), [
                                                'class' => 'btn btn-default',
                                                'data-card-switch' => 'default'
                                            ])
                                    ],
                                ],
                            ],
                        ]
                    ],
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function renderTitle()
    {
        return Html::encode($this->model->item_number);
    }

    /**
     * @inheritdoc
     */
    public function wrapperOptions()
    {
        return [
            'wrapperClass' => Block::class,
            'header' => \Yii::t('app', 'Order')
        ];
    }
}
