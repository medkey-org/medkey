<?php
namespace app\modules\crm\widgets\card;

use app\common\card\CardView;
use app\common\helpers\ArrayHelper;
use app\common\helpers\Html;
use app\common\helpers\Url;
use app\common\widgets\ActiveForm;
use app\common\wrappers\Block;
use app\modules\config\entities\CurrencyEntity;
use app\modules\crm\application\OrderServiceInterface;
use app\modules\crm\CrmModule;
use app\modules\crm\widgets\grid\OrderItemGrid;
use app\modules\medical\application\EhrServiceInterface;
use app\modules\medical\widgets\grid\ReferralGrid;
use yii\web\JsExpression;
use app\modules\crm\models\form\Order;
use app\modules\crm\models\orm\Order as OrderOrm;

/**
 * Class OrderCard
 * @package Module\CRM
 * @copyright 2012-2019 Medkey
 */
class OrderCard extends CardView
{
    /**
     * @var Order
     */
    public $model;
    /**
     * @var bool
     */
    public $wrapper = true;
    /**
     * @var EhrServiceInterface
     */
    public $ehrService;
    /**
     * @var OrderServiceInterface
     */
    public $orderService;

    /**
     * OrderCard constructor.
     * @param EhrServiceInterface $ehrService
     * @param OrderServiceInterface $orderService
     * @param array $config
     */
    public function __construct(EhrServiceInterface $ehrService, OrderServiceInterface $orderService, array $config = [])
    {
        $this->orderService = $orderService;
        $this->ehrService = $ehrService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->model = $this->orderService->getOrderForm($this->model);
        parent::init();
        $this->formOptions = array_merge($this->formOptions, [
            'action' => Url::to(['/crm/ui/order/' . $this->model->scenario, 'id' => $this->model->id]),
            'validationUrl' => Url::to(['/crm/ui/order/validate-' . $this->model->scenario, 'id' => $this->model->id]),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function dataGroups()
    {
        return [
            'main' => [
                'title' => CrmModule::t('order', 'Order details'),
                'items' => [
                    [
                        'items' => [
                            [
                                'attribute' => 'number',
                                'scenarios' => [
                                    'create' => [
                                        'value' => function (Order $model, ActiveForm $form) {
                                            return $form->field($model, 'number')
                                                ->label(false);
                                        }
                                    ]
                                ]
                            ],
                            [
                                'attribute' => 'status',
                                'scenarios' => [
                                    'create' => [
                                        'value' => function (Order $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'status')
                                                ->dropDownList(OrderOrm::statuses(), [
                                                    'readonly' => true,
                                                    'style' => 'pointer-events: none;',
                                                ])
                                                ->label(false);
                                        },
                                    ],
                                    'update' => [
                                        'value' => function (Order $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'status')
                                                ->dropDownList(OrderOrm::statuses(), [
                                                    'readonly' => true,
                                                    'style' => 'pointer-events: none;',
                                                ])
                                                ->label(false);
                                        },
                                    ],
                                    'default' => [
                                        'value' => function (Order $model) {
                                            return $model->getStatusName();
                                        }
                                    ]
                                ],
                            ],
                        ],
                    ],
                    [
                        'items' => [
                            [
                                'attribute' => 'currency',
                                'scenarios' => [
                                    'create' => [
                                        'value' => function (Order $model, ActiveForm $form) {
                                            $model->currency = 'RUB';
                                            return $form
                                                ->field($model, 'currency')
                                                ->select2(CurrencyEntity::currencyListData())
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (Order $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'currency')
                                                ->textInput(['disabled' => true])
                                                ->label(false);
                                        }
                                    ],
                                    'default' => [
                                        'value' => function (Order $model) {
                                            $currency = CurrencyEntity::findCurrency($model->currency);
                                            return $currency;
                                        }
                                    ],
                                ]
                            ],
                            [
                                'attribute' => 'currency_sum',
                                'scenarios' => [
                                    'create' => [
                                        'value' => function (Order $model, ActiveForm $form) {
                                            $model->currency_sum = '0.00';
                                            return $form->field($model, 'currency_sum')
                                                ->textInput([
                                                    'disabled' => true,
                                                ])
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (Order $model, ActiveForm $form) {
                                            if (empty($model->currency_sum)) {
                                                $model->currency_sum = '0.00';
                                            }
//                                            $money = new Money($model->currency_sum, new Currency($model->currency));
//                                            $moneyFormatter = new DecimalMoneyFormatter(new ISOCurrencies());
//                                            $model->currency_sum = $moneyFormatter->format($money);
                                            return $form->field($model, 'currency_sum')
                                                ->textInput([
                                                    'disabled' => true,
                                                ])
                                                ->label(false);
                                        }
                                    ],
                                    'default' => [
                                        'value' => function (Order $model) {
                                            $currency = CurrencyEntity::findCurrency($model->currency);
                                            $cur = $currency;
                                            if (empty($model->currency_sum)) {
                                                $model->currency_sum = '0.00';
                                            }
                                            return $model->currency_sum . ' ' . $cur;
//                                            $money = new Money($model->currency_sum, new Currency($model->currency));
//                                            $moneyFormatter = new DecimalMoneyFormatter(new ISOCurrencies());
//                                            return $moneyFormatter->format($money);
                                        }
                                    ]
                                ],
                            ],
                        ]
                    ],
                    [
                        'items' => [
                            [
                                'attribute' => 'description',
                                'colSize' => 6,
                            ],
                            [
                                'attribute' => 'ehr_id',
                                'colSize' => 6,
                                'scenarios' => [
                                    'default' => [
                                        'value' => function (Order $model) {
                                            if (empty($model->ehr['number'])) {
                                                return '';
                                            }
                                            return $model->ehr['number'];
                                        }
                                    ],
                                    'create' => [
                                        'value' => function (Order $model, ActiveForm $form) {
                                            if (!empty($model->ehr_id)) {
                                                $ehr = $this->ehrService->getEhrById($model->ehr_id);
                                            }
                                            return $form->field($model, 'ehr_id')
                                                ->select2(!isset($ehr) ? [] : ArrayHelper::map([$ehr], 'id', 'number'), [], [
                                                    'allowClear' => true,
                                                    'minimumInputLength' => 1,
                                                    'language' => [
                                                        'errorLoading' => new JsExpression("function () { return 'Ничего не найдено.'; }"),
                                                    ],
                                                    'ajax' => [
                                                        'url' => Url::to(['/medical/rest/ehr/index']),
                                                        'dataType' => 'json',
                                                        'delay' => 1000,
                                                        'data' => new JsExpression('function (params) { return {q:params.term, page: params.page || 1}; }'),
                                                        'processResults' => new JsExpression('function (data, params) { return { results: data, pagination: {more: (params.page * 10) < data.count_filtered}}; }')
                                                    ],
                                                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                                    'templateResult' => new JsExpression('function (ehr) { if (ehr.loading) { return ehr.text; } else {return ehr.number;} }'),
                                                    'templateSelection' => new JsExpression('function (ehr) { if (ehr.number) {return ehr.number;} else { return ehr.text;} }'),
                                                ])
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (Order $model, ActiveForm $form) {
                                            $model->ehr;
                                            return $form->field($model, 'ehr_id')
                                                ->select2(ArrayHelper::map([$model->ehr], 'id', 'number'), [], [
                                                    'allowClear' => true,
                                                    'minimumInputLength' => 1,
                                                    'language' => [
                                                        'errorLoading' => new JsExpression("function () { return 'Ничего не найдено.'; }"),
                                                    ],
                                                    'ajax' => [
                                                        'url' => Url::to(['/medical/rest/ehr/index']),
                                                        'dataType' => 'json',
                                                        'delay' => 1000,
                                                        'data' => new JsExpression('function (params) { return {q:params.term, page: params.page || 1}; }'),
                                                        'processResults' => new JsExpression('function (data, params) { return { results: data, pagination: {more: (params.page * 10) < data.count_filtered}}; }')
                                                    ],
                                                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                                    'templateResult' => new JsExpression('function (ehr) { if (ehr.loading) { return ehr.text; } else {return ehr.number;} }'),
                                                    'templateSelection' => new JsExpression('function (ehr) { if (ehr.number) {return ehr.number;} else { return ehr.text;} }'),
                                                ])
                                                ->label(false);
                                        }
                                    ],
                                ]
                            ]
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
    public function subpanels()
    {
        return [
            'orderItems' => [
                'value' => function ($model) {
                    return OrderItemGrid::widget([
                        'orderId' => $model->id,
                        'cardId' => $this->getId(),
                    ]);
                },
                'header' => CrmModule::t('order', 'Positions')
            ],
            'referrals' => [
                'value' => function ($model) {
                    return ReferralGrid::widget([
                        'orderId' => $model->id,
                        'actionButtonTemplate' => '{refresh}{record}',
                    ]);
                },
                'header' => CrmModule::t('order', 'Attendances')
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function renderTitle()
    {
        return Html::encode($this->model->number);
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
