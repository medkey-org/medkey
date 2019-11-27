<?php
namespace app\modules\medical\widgets\grid;

use app\common\base\UniqueKey;
use app\common\button\LinkActionButton;
use app\common\db\ActiveRecord;
use app\common\grid\GridView;
use app\common\helpers\ArrayHelper;
use app\common\helpers\CommonHelper;
use app\common\helpers\Html;
use app\common\helpers\Url;
use app\modules\medical\models\finders\AttendanceFilter;
use app\modules\medical\models\orm\Attendance;
use app\modules\medical\models\orm\Ehr;
use app\modules\medical\application\AttendanceServiceInterface;
use app\modules\medical\application\EhrServiceInterface;
use app\modules\medical\application\PatientServiceInterface;
use app\modules\medical\application\ReferralServiceInterface;
use app\modules\medical\models\orm\Patient;
use app\modules\organization\models\orm\Employee;
use kartik\select2\Select2;
use yii\web\JsExpression;

/**
 * Class AttendanceGrid
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class AttendanceGrid extends GridView
{
    /**
     * @var AttendanceFilter
     */
    public $filterModel;
    /**
     * @var AttendanceServiceInterface
     */
    public $attendanceService;
    /**
     * @var ReferralServiceInterface
     */
    public $referralService;
    /**
     * @var string|int
     */
    public $referralId;
    /**
     * @var string|int
     */
    public $patientId;
    /**
     * @var bool
     */
    public $visibleFilterRow = true;
    /**
     * @var bool
     */
    public $visibleActionButtons = true;
    public $actionButtonTemplate = '{refresh}{ehr-record}';

    public function __construct(ReferralServiceInterface $referralService, AttendanceServiceInterface $attendanceService, array $config = [])
    {
        $this->attendanceService = $attendanceService;
        $this->referralService = $referralService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->filterModel = AttendanceFilter::ensure($this->filterModel, 'search', $this->formData);
        $this->filterModel->referralId = $this->referralId;
        $this->filterModel->patientId = $this->patientId;

        $employee = \Yii::$app->user->getIdentity()->employee;
        if (isset($employee)) {
            $this->filterModel->employeeId = $employee->id;
        }

        $this->dataProvider = $this->attendanceService->getAttendanceList($this->filterModel);
//        $this->actionButtons['create'] = [
//            'class' => LinkActionButton::class,
//            'url' => ['/medical/ui/attendance/view', 'scenario' => ActiveRecord::SCENARIO_CREATE],
//            'isDynamicModel' => false,
//            'isAjax' => false,
//            'disabled' => false,
//            'value' => '',
//            'options' => [
//                'class' => 'btn btn-xs btn-primary',
//                'icon' => 'plus',
//            ],
//        ];
//        $this->actionButtons['delete'] = [
//            'class' => LinkActionButton::class,
//            'url' => ['/medical/rest/attendance/delete'],
//            'isDynamicModel' => true,
//            'isAjax' => true,
//            'disabled' => true,
//            'isConfirm' => true,
//            'afterUpdateBlock' => $this,
//            'value' => '',
//            'options' => [
//                'class' => 'btn btn-xs btn-danger',
//                'icon' => 'remove',
//            ],
//        ];
        $this->actionButtons['ehr-record'] = [
            'class' => LinkActionButton::class,
            'url' => ['/medical/ui/ehr-record/view'],
            'isDynamicModel' => true,
            'isAjax' => false,
            'disabled' => true,
            'isConfirm' => false,
            'afterUpdateBlock' => $this,
            'value' => '',
            'primaryAttribute' => 'ehrId',
            'options' => [
                'class' => 'btn btn-xs btn-primary',
                'icon' => 'plus',
            ],
        ];
        $this->columns = [
            [
                'attribute' => 'datetime',
                'value' => function (Attendance $model) {
                    return Html::a(
                        \Yii::$app->formatter->asDatetime($model->datetime . date_default_timezone_get(), CommonHelper::FORMAT_DATETIME_UI),
                        ['/medical/ui/attendance/view', 'id' => $model->id]
                    );
                },
                'filter' => function () {
                    return Html::activeDateTimeInput($this->filterModel, 'datetime', ['class' => 'form-control']);
                },
                'format' => 'html',
                'options' => [
                    'class' => 'col-xs-2'
                ],
            ],
//            [
//                'attribute' => 'employee_id',
//                'value' => function (Attendance $model) {
//                    if ($model->employee instanceof Employee) {
//                        return $model->employee->fullName;
//                    }
//                    return '';
//                },
//                'filter' => function () {
//                    return Select2::widget([
//                        'model' => $this->filterModel,
//                        'attribute' => 'employeeId',
//                        'data' => ArrayHelper::map(Employee::find()->notDeleted()->all(), 'id', 'fullName'),
//                        'options' => [
//                            'id' => UniqueKey::generate('employeeId'),
//                            'placeholder' => \Yii::t('app', 'Select value...'),
//                        ],
//                        'pluginOptions' => [
//                            'allowClear' => true,
//                        ]
//                    ]);
//                }
//            ],
            [
                'attribute' => 'ehr_id',
                'value' => function (Attendance $model) {
                    if (!$model->ehr instanceof Ehr) {
                        return '';
                    }
                    return Html::a($model->ehr->number, ['/medical/ui/ehr/view', 'id' => $model->ehr->id]);
                },
                'filter' => function () {
                    $data = [];
                    if (!empty($this->filterModel->ehrId)) {
                        $data = ArrayHelper::map(
                            [\Yii::$container->get(EhrServiceInterface::class)->getEhrById($this->filterModel->ehrId)],
                            'id',
                            'number'
                        );
                    }
                    return Select2::widget([
                        'model' => $this->filterModel,
                        'attribute' => 'ehrId',
                        'data' => $data,
                        'options' => [
                            'id' => UniqueKey::generate('ehrId'),
                            'placeholder' => \Yii::t('app', 'Select value...'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 1,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return '" . \Yii::t('app', 'Nothing found') ."'; }"),
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
                        ]
                    ]);
                },
                'format' => 'html',
            ],
            [
                'attribute' => 'patient_id',
                'value' => function (Attendance $model) {
                    if (!$model->ehr instanceof Ehr || !$model->ehr->patient instanceof Patient) {
                        return '';
                    }
                    return $model->ehr->patient->fullName;
                },
                'filter' => function () {
                    $data = [];
                    if (!empty($this->filterModel->patientId)) {
                        $data = ArrayHelper::map(
                            [\Yii::$container->get(PatientServiceInterface::class)->getPatientById($this->filterModel->patientId)],
                            'id',
                            'fullName'
                        );
                    }
                    return Select2::widget([
                        'model' => $this->filterModel,
                        'attribute' => 'patientId',
                        'data' => $data,
                        'options' => [
                            'id' => UniqueKey::generate('patientId'),
                            'placeholder' => \Yii::t('app', 'Select value...'),
                        ],
                        'pluginOptions' => [
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
                        ]
                    ]);
                }
            ],
            [
                'attribute' => 'status',
                'value' => function (Attendance $model) {
                    return $model->getStatusName();
                },
                'filter' => function () {
                    return Html::activeSelect2Input(
                        $this->filterModel,
                        'status',
                        Attendance::statuses(),
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
                'format' => 'html',
            ],
            [
                'attribute' => 'type',
                'value' => function (Attendance $model) {
                    return $model->typeName();
                },
                'filter' => function () {
                    return Html::activeSelect2Input(
                        $this->filterModel,
                        'type',
                        Attendance::types(),
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
                'format' => 'html'
            ],
        ];
        parent::init();
    }
}
