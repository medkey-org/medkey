<?php
namespace app\modules\medical\widgets\card;

use app\common\card\CardView;
use app\common\helpers\ArrayHelper;
use app\common\helpers\Html;
use app\common\helpers\Url;
use app\common\widgets\ActiveForm;
use app\common\wrappers\Block;
use app\modules\crm\widgets\grid\OrderGrid;
use app\modules\medical\MedicalModule;
use app\modules\medical\models\orm\Ehr;
use app\modules\medical\models\orm\Patient;
use app\modules\medical\application\EhrServiceInterface;
use app\modules\medical\application\PatientServiceInterface;
use app\modules\medical\widgets\grid\EhrRecordGrid;
use app\modules\medical\widgets\grid\ReferralGrid;
use yii\web\JsExpression;

/**
 * Class EhrCard
 *
 * @property EhrServiceInterface $applicationService
 *
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class EhrCard extends CardView
{
    /**
     * @var Ehr
     */
    public $model;
    /**
     * @var Patient
     */
    public $patient;
    /**
     * @var PatientServiceInterface
     */
    public $patientService;
    /**
     * @var bool
     */
    public $wrapper = true;

    /**
     * EhrCard constructor.
     * @param PatientServiceInterface $patientService
     * @param array $config
     */
    public function __construct(PatientServiceInterface $patientService, array $config = [])
    {
        $this->patientService = $patientService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
//        $this->patient = Patient::ensureWeak($this->patient);
//        empty($this->patient->id) ?: $this->model->patient_id = $this->patient->id;
        $this->formOptions = array_merge($this->formOptions, [
            'action' => Url::to(['/medical/rest/ehr/' . $this->model->scenario, 'id' => $this->model->id]),
            'validationUrl' => Url::to(['/medical/rest/ehr/validate-' . $this->model->scenario, 'id' => $this->model->id]),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function title()
    {
        return Html::encode($this->model->number);
    }

    /**
     * @inheritdoc
     */
    public function dataGroups()
    {
        return [
            'ehr' => [
                'title' => MedicalModule::t('ehr', 'EHR details'),
                'items' => [
                    [
                        'items' => [
                            [
                                'attribute' => 'type',
                                'scenarios' => [
                                    'default' => [
                                        'value' => function (Ehr $model) {
                                            return Html::encode($model->getTypeName());
                                        }
                                    ],
                                    'create' => [
                                        'value' => function (Ehr $model, ActiveForm $form) {
                                            $model->type = Ehr::TYPE_AMBULATORY;
                                            return $form
                                                ->field($model, 'type')
                                                ->select2(Ehr::types())
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (Ehr $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'type')
                                                ->select2(Ehr::types())
                                                ->label(false);
                                        }
                                    ]
                                ],
                            ],
                            [
                                'attribute' => 'status',
                                'scenarios' => [
                                    'default' => [
                                        'value' => function (Ehr $model) {
                                            return Html::encode($model->getStatusName());
                                        }
                                    ],
                                    'create' => [
                                        'value' => function (Ehr $model, ActiveForm $form) {
                                            $model->status = Ehr::STATUS_ACTIVE;
                                            return $form
                                                ->field($model, 'status')
                                                ->select2(Ehr::statusListData())
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (Ehr $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'status')
                                                ->select2(Ehr::statusListData())
                                                ->label(false);
                                        }
                                    ]
                                ],
                            ],
                        ],
                    ],
                    [
                        'items' => [
                            'number',
                            [
                                'attribute' => 'patient_id',
                                'scenarios' => [
                                    'default' => [
                                        'value' => function (Ehr $model) {
                                            if ($model->patient instanceof Patient) {
                                                return Html::a(Html::encode($model->patient->fullName), Url::to(['/medical/ui/patient/view/', 'id' => $model->patient->id]));
                                            }
                                            return '';
                                        }
                                    ],
                                    'create' => [
                                        'value' => function (Ehr $model, ActiveForm $form) {
                                            if (!empty($model->patient_id)) {
                                                $patient = $this->patientService->getPatientById($model->patient_id);
                                            }
                                            return $form
                                                ->field($model, 'patient_id')
                                                ->select2(!isset($patient) ? [] : ArrayHelper::map([$patient], 'id', function ($row) {
                                                    return empty($row) ?: Html::encode($row->last_name . ' ' . $row->first_name . ' ' . $row->middle_name);
                                                }), [], [
                                                    'allowClear' => true,
                                                    'minimumInputLength' => 1,
                                                    'language' => [
                                                        'errorLoading' => new JsExpression("function () { return 'Ничего не найдено.'; }"),
                                                    ],
                                                    'ajax' => [
                                                        'url' => Url::to(['/medical/rest/patient/index']),
                                                        'dataType' => 'json',
                                                        'delay' => 1000,
                                                        'data' => new JsExpression('function (params) { return {q:params.term, page: params.page || 1}; }'),
                                                        'processResults' => new JsExpression('function (data, params) { return { results: data, pagination: {more: (params.page * 10) < data.count_filtered}}; }')
                                                    ],
                                                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                                    'templateResult' => new JsExpression('function (patient) { if (patient.loading) { return patient.text; } else {return patient.last_name + " " + patient.first_name + " " + patient.middle_name;} }'),
                                                    'templateSelection' => new JsExpression('function (patient) { if (patient.last_name) {return patient.last_name + " " + patient.first_name + " " + patient.middle_name;} else { return patient.text;} }'),
                                                ])
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (Ehr $model, ActiveForm $form) {
                                            $model->patient; // load rel
                                            return $form
                                                ->field($model, 'patient_id')
                                                ->select2(ArrayHelper::map([$model->patient], 'id', function ($row) {
                                                    return empty($row) ?: Html::encode($row->last_name . ' ' . $row->first_name . ' ' . $row->middle_name);
                                                }), [], [
                                                    'allowClear' => true,
                                                    'minimumInputLength' => 1,
                                                    'language' => [
                                                        'errorLoading' => new JsExpression("function () { return 'Ничего не найдено.'; }"),
                                                    ],
                                                    'ajax' => [
                                                        'url' => Url::to(['/medical/rest/patient/index']),
                                                        'dataType' => 'json',
                                                        'delay' => 1000,
                                                        'data' => new JsExpression('function (params) { return {q:params.term, page: params.page || 1}; }'),
                                                        'processResults' => new JsExpression('function (data, params) { return { results: data, pagination: {more: (params.page * 10) < data.count_filtered}}; }')
                                                    ],
                                                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                                    'templateResult' => new JsExpression('function (patient) { if (patient.loading) { return patient.text; } else {return patient.last_name + " " + patient.first_name + " " + patient.middle_name;} }'),
                                                    'templateSelection' => new JsExpression('function (contact) { if (contact.last_name) {return contact.last_name + " " + contact.first_name + " " + contact.middle_name;} else { return contact.text;} }'),
                                                ])
                                                ->label(false);
                                        }
                                    ],
                                ],
                            ],
                        ]
                    ]
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
     * @inheritdoc
     */
    public function subpanels()
    {
        return [
            'ehrRecords' => [
                'value' => function ($model) {
                    return EhrRecordGrid::widget([
                        'ehrId' => $model->id
                    ]);
                },
                'header' => MedicalModule::t('ehr', 'EHR records'),
            ],
            'order' => [
                'value' => function ($model) {
                    return OrderGrid::widget([
                        'ehr' => $model
                    ]);
                },
                'header' => MedicalModule::t('ehr', 'Orders'),
            ],
            'referrals' => [
                'value' => function ($model) {
                    return ReferralGrid::widget([
                        'ehr' => $model
                    ]);
                },
                'header' => MedicalModule::t('ehr', 'Referrals'),
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
            'header' => MedicalModule::t('ehr', 'EHR'),
        ];
    }
}
