<?php
namespace app\modules\medical\widgets\card;

use app\common\card\CardView;
use app\common\helpers\ArrayHelper;
use app\common\helpers\CommonHelper;
use app\common\helpers\Html;
use app\common\helpers\Url;
use app\common\widgets\ActiveForm;
use app\common\wrappers\Block;
use app\modules\medical\application\AttendanceServiceInterface;
use app\modules\medical\MedicalModule;
use app\modules\medical\models\form\Attendance;
use app\modules\medical\models\orm\Attendance as AttendanceORM;
use app\modules\organization\models\orm\Employee;
use yii\web\JsExpression;

/**
 * Class AttendanceCard
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class AttendanceCard extends CardView
{
    /**
     * @var Attendance
     */
    public $model;
    /**
     * @var bool
     */
    public $wrapper = true;
    /**
     * @var AttendanceServiceInterface
     */
    public $attendanceService;

    /**
     * AttendanceCard constructor.
     * @param AttendanceServiceInterface $attendanceService
     * @param array $config
     */
    public function __construct(AttendanceServiceInterface $attendanceService, array $config = [])
    {
        $this->attendanceService = $attendanceService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->model = $this->attendanceService->getAttendanceForm($this->model);
        parent::init();
        $this->formOptions = array_merge($this->formOptions, [
            'action' => Url::to(['/medical/rest/attendance/' . $this->model->scenario, 'id' => $this->model->id]),
            'validationUrl' => Url::to(['/medical/rest/attendance/validate-' . $this->model->scenario, 'id' => $this->model->id]),
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
                'title' => MedicalModule::t('common', 'Attendance\'s data'),
                'items' => [
                    [
                        'items' => [
                            [
                                'attribute' => 'type',
                                'scenarios' => [
                                    'default' => [
                                        'value' => function (Attendance $model) {
                                            return Html::encode($model->typeName());
                                        }
                                    ],
                                    'create' => [
                                        'value' => function (Attendance $model, ActiveForm $form) {
                                            return $form
                                                ->field($model, 'type')
                                                ->select2(AttendanceORM::types())
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (Attendance $model, ActiveForm $form) {
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
                    [
                        'items' => [
                            [
                                'attribute' => 'datetime',
                                'scenarios' => [
                                    'default' => [
                                        'value' => function (Attendance $model) {
                                            return Html::encode(\Yii::$app->formatter->asDatetime($model->datetime . date_default_timezone_get(), CommonHelper::FORMAT_DATETIME_UI));
                                        }
                                    ],
                                    'create' => [
                                        'value' => function (Attendance $model, ActiveForm $form) {
                                            return $form->field($model, 'datetime')
                                                ->dateTimeInput()
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (Attendance $model, ActiveForm $form) {
                                            return $form->field($model, 'datetime')
                                                ->dateTimeInput(['disabled' => true])
                                                ->label(false);
                                        }
                                    ],
                                ],
                            ],
                            [
                                'attribute' => 'ehr_id',
                                'scenarios' => [
                                    'default' => [
                                        'value' => function (Attendance $model) {
                                            if (!$model['ehr']) {
                                                return '';
                                            }
                                            return Html::encode($model->ehr['number']);
                                        }
                                    ],
                                    'create' => [
                                        'value' => function (Attendance $model, ActiveForm $form) {
                                            return $form->field($model, 'ehr_id')
                                                ->select2([], [], [
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
                                        'value' => function (Attendance $model, ActiveForm $form) {
//                                            $model->ehr;
                                            return $form->field($model, 'ehr_id')
                                                ->select2(ArrayHelper::map([$model['ehr']], 'id', 'number'), [], [
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
                    [
                        'items' => [
                            [
                                'colSize' => '6',
                                'attribute' => 'employee_id',
                                'scenarios' => [
                                    'create' => [
                                        'value' => function (Attendance $model, ActiveForm $form) {
                                            return $form->field($model, 'employee_id')
                                                ->select2(ArrayHelper::map(Employee::find()->notDeleted()->all(), 'id', function ($row) {
                                                    return empty($row) ?: $row->last_name . ' ' . $row->first_name . ' ' . $row->middle_name;
                                                }), [])
                                                ->label(false);
                                        }
                                    ],
                                    'update' => [
                                        'value' => function (Attendance $model, ActiveForm $form) {
                                            return $form->field($model, 'employee_id')
                                                ->select2(ArrayHelper::map(Employee::find()->notDeleted()->all(), 'id', function ($row) {
                                                    return empty($row) ?: $row->last_name . ' ' . $row->first_name . ' ' . $row->middle_name;
                                                }), [])
                                                ->label(false);
                                        }
                                    ],
                                    'default' => [
                                        'value' => function (Attendance $model) {
                                            if (!$model['employee']) {
                                                return '';
                                            }
                                            return Html::encode($model['employee']['last_name'] . ' ' . $model['employee']['first_name'] . ' ' . $model['employee']['middle_name']);
                                        }
                                    ]
                                ]
                            ]
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

//    /**
//     * @inheritdoc
//     */
//    public function subpanels()
//    {
//        return [
//            'referrals' => [
//                'value' => function ($model) {
//                    return EhrRecordGrid::widget([
//                        'ehr' => $model
//                    ]);
//                },
//                'header' => MedicalModule::t('common', 'Attendance\'s card')
//            ]
//        ];
//    }

    /**
     * @inheritdoc
     */
    public function wrapperOptions()
    {
        return [
            'wrapperClass' => Block::class,
            'header' => MedicalModule::t('common', 'Attendance')
        ];
    }
}
