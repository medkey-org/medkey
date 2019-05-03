<?php
namespace app\modules\medical\widgets\card;

use app\common\card\CardView;
use app\common\helpers\CommonHelper;
use app\common\helpers\Html;
use app\common\helpers\Url;
use app\common\widgets\ActiveForm;
use app\common\wrappers\Block;
use app\modules\config\entities\CurrencyEntity;
use app\modules\medical\application\ServicePriceServiceInterface;
use app\modules\medical\MedicalModule;
use app\modules\medical\models\form\ServicePriceList;
use app\modules\medical\models\orm\ServicePriceList as ServicePriceListORM;
use app\modules\medical\widgets\grid\ServicePriceGrid;

/**
 * Class ServicePriceListCard
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ServicePriceListCard extends CardView
{
    /**
     * @var ServicePriceList
     */
    public $model;
    /**
     * @var bool
     */
    public $wrapper = true;
    /**
     * @var ServicePriceServiceInterface
     */
    public $servicePriceService;


    /**
     * ServicePriceListCard constructor.
     * @param ServicePriceServiceInterface $servicePriceService
     * @param array $config
     */
    public function __construct(ServicePriceServiceInterface $servicePriceService, array $config = [])
    {
        $this->servicePriceService = $servicePriceService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->model = $this->servicePriceService->getServicePriceListForm($this->model, 'default');
        parent::init();
        $this->formOptions = array_merge($this->formOptions, [
            'action' => Url::to(['/medical/rest/service-price-list/' . $this->model->scenario, 'id' => $this->model->id]),
            'validationUrl' => Url::to(['/medical/rest/service-price-list/validate-' . $this->model->scenario, 'id' => $this->model->id]),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function dataGroups()
    {
        return [
            'personal' => [
                'title' => MedicalModule::t('servicePriceList', 'Pricelist details'),
                'items' => [
                    [
                        'items' => [
                            'name',
                            [
                                'attribute' => 'status',
                                'scenarios' => [
                                    'create' => [
                                        'value' => function (ServicePriceList $model, ActiveForm $form) {
                                            $model->status = ServicePriceListORM::STATUS_ACTIVE;
                                            return $form
                                                ->field($model, 'status')
                                                ->select2(ServicePriceListORM::statuses())
                                                ->label(false);
                                        },
                                    ],
                                    'update' => [
                                        'value' => function (ServicePriceList $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'status')
                                                ->select2(ServicePriceListORM::statuses())
                                                ->label(false);
                                        },
                                    ],
                                    'default' => [
                                        'value' => function (ServicePriceList $model) {
                                            return Html::encode($model->getStatusName());
                                        }
                                    ]
                                ],
                            ],
                        ]
                    ],
                    [
                        'items' => [
                            [
                                'attribute' => 'start_date',
                                'scenarios' => [
                                    'default' => [
                                        'value' => function (ServicePriceList $model) {
                                            return Html::encode(\Yii::$app->formatter->asDate($model->start_date, CommonHelper::FORMAT_DATE_UI));
                                        },
                                    ],
                                    'update' => [
                                        'value' => function (ServicePriceList $model, ActiveForm $form) {
                                            return $form->field($model, 'start_date')->dateInput()->label(false);
                                        }
                                    ],
                                    'create' => [
                                        'value' => function (ServicePriceList $model, ActiveForm $form) {
                                            return $form->field($model, 'start_date')->dateInput()->label(false);
                                        }
                                    ],
                                ]
                            ],
                            [
                                'attribute' => 'end_date',
                                'scenarios' => [
                                    'default' => [
                                        'value' => function (ServicePriceList $model) {
                                            return Html::encode(\Yii::$app->formatter->asDate($model->end_date, CommonHelper::FORMAT_DATE_UI));
                                        },
                                    ],
                                    'update' => [
                                        'value' => function (ServicePriceList $model, ActiveForm $form) {
                                            return $form->field($model, 'end_date')->dateInput()->label(false);
                                        }
                                    ],
                                    'create' => [
                                        'value' => function (ServicePriceList $model, ActiveForm $form) {
                                            return $form->field($model, 'end_date')->dateInput()->label(false);
                                        }
                                    ],
                                ]
                            ],
                        ]
                    ],
                    [
                        'items' => [
                            [
                                'colSize' => 6,
                                'attribute' => 'currency',
                                'scenarios' => [
                                    'create' => [
                                        'value' => function (ServicePriceList $model, ActiveForm $form) {
                                            $model->currency = 'RUB';
                                            return $form
                                                ->field($model, 'currency')
                                                ->select2(CurrencyEntity::currencyListData())
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (ServicePriceList $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'currency')
                                                ->textInput([
                                                    'disabled' => true
                                                ])
                                                ->label(false);
                                        }
                                    ],
                                    'default' => [
                                        'value' => function (ServicePriceList $model) {
                                            $currency = CurrencyEntity::findCurrency($model->currency);
                                            return Html::encode($currency);
                                        }
                                    ],
                                ]
                            ],
                        ]
                    ],
                ]
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
            'workplans' => [
                'value' => function ($model) {
                    return ServicePriceGrid::widget([
                        'servicePriceListId' => $model->id,
//                        'visibleActionButtons' => false,
                    ]);
                },
                'header' => MedicalModule::t('servicePriceList', 'Pricelist positions'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function wrapperOptions()
    {
        return [
            'wrapperClass' => Block::class,
            'header' => MedicalModule::t('servicePriceList', 'Pricelist'),
        ];
    }
}
