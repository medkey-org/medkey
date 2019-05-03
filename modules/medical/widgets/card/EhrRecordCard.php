<?php
namespace app\modules\medical\widgets\card;

use app\common\card\CardView;
use app\common\helpers\Html;
use app\common\helpers\Url;
use app\common\widgets\ActiveForm;
use app\common\wrappers\DynamicModal;
use app\modules\medical\application\EhrServiceInterface;
use app\modules\medical\MedicalModule;
use app\modules\medical\models\form\EhrRecord;

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
     * @inheritdoc
     */
    public function init()
    {
        $this->model = $this->ehrService->getEhrRecordFormByRaw($this->model);
        parent::init();
        $this->formOptions = array_merge($this->formOptions, [
            'action' => Url::to(['/medical/rest/ehr-record/' . $this->model->scenario, 'id' => $this->model->id]),
            'validationUrl' => Url::to(['/medical/rest/ehr-record/validate-' . $this->model->scenario, 'id' => $this->model->id]),
        ]);
    }

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
    public function dataGroups()
    {
        return [
            'ehr' => [
                'title' => MedicalModule::t('common', 'EHR details'),
                'items' => [
                    [
                        'items' => [
                            [
                                'attribute' => '',
                                'scenarios' => [
                                    'default' => [
                                        'value' => function (EhrRecord $model) {
                                            return Html::encode($model->typeName());
                                        }
                                    ],
                                    'create' => [
                                        'value' => function (EhrRecord $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'type')
                                                ->select2(AttendanceORM::types())
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (EhrRecord $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'type')
                                                ->select2(AttendanceORM::types())
                                                ->label(false);
                                        }
                                    ]
                                ],
                            ],
                            [
                                'attribute' => 'status',
                                'scenarios' => [
                                    'default' => [
                                        'value' => function (Attendance $model) {
                                            return Html::encode($model->getStatusName());
                                        }
                                    ],
                                    'create' => [
                                        'value' => function (Attendance $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'status')
                                                ->select2(AttendanceORM::statuses())
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (Attendance $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'status')
                                                ->select2(AttendanceORM::statuses())
                                                ->label(false);
                                        }
                                    ]
                                ],
                            ],
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
                                                'icon'  => 'saved'
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
     * {@inheritdoc}
     */
    public function wrapperOptions()
    {
        return [
            'wrapperClass' => DynamicModal::class,
            'header' => MedicalModule::t('ehr', 'EHR record')
        ];
    }
}