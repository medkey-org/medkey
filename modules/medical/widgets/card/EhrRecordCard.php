<?php
namespace app\modules\medical\widgets\card;

use app\common\card\CardView;
use app\common\helpers\ArrayHelper;
use app\common\helpers\CommonHelper;
use app\common\helpers\Html;
use app\common\helpers\Url;
use app\common\widgets\ActiveForm;
use app\common\wrappers\DynamicModal;
use app\modules\medical\application\EhrServiceInterface;
use app\modules\medical\MedicalModule;
use app\modules\medical\models\form\EhrRecord;
use app\modules\organization\models\orm\Employee;

class EhrRecordCard extends CardView
{
    /**
     * @var EhrRecord
     */
    public $model;
    /**
     * @var mixed foreign key
     */
    public $ehrId;
    /**
     * @var bool
     */
    public $wrapper = true;
    /**
     * @var EhrServiceInterface
     */
    public $ehrService;

    /**
     * EhrRecordCard constructor.
     * @param EhrServiceInterface $ehrService
     * @param array $config
     */
    public function __construct(EhrServiceInterface $ehrService, array $config = [])
    {
        $this->ehrService = $ehrService;
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->model = $this->ehrService->getEhrRecordFormByRaw($this->model, $this->ehrId);
        parent::init();
        $this->formOptions = array_merge($this->formOptions, [
            'action' => Url::to(['/medical/rest/ehr-record/' . $this->model->scenario, 'id' => $this->model->id]),
            'validationUrl' => Url::to(['/medical/rest/ehr-record/validate-' . $this->model->scenario, 'id' => $this->model->id]),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function visibleScenarioButtons()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function dataGroups()
    {
        return [
            'ehr' => [
                'title' => MedicalModule::t('common', 'EHR details'),
                'items' => [
                    [
                        'items' => [
                            [
                                'attribute' => 'datetime',
                                'scenarios' => [
                                    'default' => [
                                        'value' => function (EhrRecord $model) {
                                            return Html::encode(\Yii::$app->formatter->asDatetime($model->datetime . date_default_timezone_get(), CommonHelper::FORMAT_DATETIME_UI));
                                        }
                                    ],
                                    'create' => [
                                        'value' => function (EhrRecord $model, ActiveForm $form) {
                                            return $form->field($model, 'datetime')
                                                ->dateTimeInput()
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (EhrRecord $model, ActiveForm $form) {
                                            return $form->field($model, 'datetime')
                                                ->dateTimeInput(['disabled' => true])
                                                ->label(false);
                                        }
                                    ],
                                ],
                            ],
                           [
                                'attribute' => 'revist',
                                'scenarios' => [
                                    'default' => [
                                        'value' => function (EhrRecord $model) {
                                            return Html::encode(\Yii::$app->formatter->asDatetime($model->datetime . date_default_timezone_get(), CommonHelper::FORMAT_DATETIME_UI));
                                        }
                                    ],
                                    'create' => [
                                        'value' => function (EhrRecord $model, ActiveForm $form) {
                                            return $form->field($model, 'revist')
                                                ->dateTimeInput()
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (EhrRecord $model, ActiveForm $form) {
                                            return $form->field($model, 'revist')
                                                ->dateTimeInput(['disabled' => true])
                                                ->label(false);
                                        }
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'items' => [
                            'complaints',
                            'diagnosis',
                        ],
                    ],
                    [
                        'items' => [
                            'conclusion',
                            'recommendations',
                        ],
                    ],
                    [
                        'items' => [
                            [
                                'attribute' => 'preliminary',
                                'colSize' => 6,
                            ],
                        ],
                    ],
                    [
                        'items' => [
                            [
                                'attribute' => 'ehr_id',
                                'scenarios' => [
                                    'create' => [
                                        'value' => function (EhrRecord $model, ActiveForm $form) {
                                            return $form->field($model, 'ehr_id')
                                                ->hiddenInput()
                                                ->label(false);

                                        },
                                        'label' => false,
                                    ],
                                    'update' => [
                                        'value' => function (EhrRecord $model, ActiveForm $form) {
                                            return $form->field($model, 'ehr_id')
                                                ->hiddenInput()
                                                ->label(false);
                                        },
                                        'label' => false,
                                    ],
                                    'default' => [
                                        'value' => false,
                                        'label' => false,
                                    ],
                                ]
                            ],
                            [
                                'attribute' => 'employee_id',
                                'scenarios' => [
                                    'create' => [
                                        'value' => function (EhrRecord $model, ActiveForm $form) {
                                            return $form->field($model, 'employee_id')
                                                ->hiddenInput()
                                                ->label(false);
                                        },
                                        'label' => false,
                                    ],
                                    'update' => [
                                        'value' => function (EhrRecord $model, ActiveForm $form) {
                                            return $form->field($model, 'employee_id')
                                                ->hiddenInput()
                                                ->label(false);
                                        },
                                        'label' => false,
                                    ],
                                    'default' => [
                                        'value' => false,
                                        'label' => false,
                                    ]
                                ]
                            ]
                        ],
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
                                                'icon' => 'saved'
                                            ])
                                            . '&nbsp' . Html::button(\Yii::t('app', 'Cancel'), [
                                                'class' => 'btn btn-default',
                                                'data-card-switch' => 'default'
                                            ])
                                    ],
                                    'create' => [
                                        'label' => false,
                                        'value' =>
                                            Html::submitButton(\Yii::t('app', 'Save'), [
                                                'class' => 'btn btn-primary',
                                                'icon' => 'saved'
                                            ])
                                            . '&nbsp' . Html::button(\Yii::t('app', 'Cancel'), [
                                                'class' => 'btn btn-default',
                                                'data-dismiss' => 'modal'
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
     * {@inheritdoc}
     */
    public function wrapperOptions()
    {
        return [
            'wrapperClass' => DynamicModal::class,
            'header' => MedicalModule::t('ehr', 'EHR record'),
            'size' => 'modal-lg'
        ];
    }
}
