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
use app\modules\medical\application\AttendanceServiceInterface;
use app\modules\medical\application\EhrServiceInterface;
use app\modules\medical\application\PatientServiceInterface;
use app\modules\medical\MedicalModule;
use app\modules\medical\models\finders\AttendanceFilter;
use app\modules\medical\models\finders\PatientAttendanceFilter;
use app\modules\medical\models\orm\Attendance;
use app\modules\medical\models\orm\Ehr;
use app\modules\medical\models\orm\Patient;
use app\modules\medical\models\orm\Speciality;
use app\modules\organization\models\orm\Cabinet;
use app\modules\organization\models\orm\Employee;
use kartik\select2\Select2;
use yii\web\JsExpression;

class PatientAttendanceGrid extends GridView
{
    /**
     * @var PatientAttendanceFilter
     */
    public $filterModel;
    /**
     * @var AttendanceServiceInterface
     */
    public $attendanceService;
    /**
     * @var string
     */
    public $patientId;
    /**
     * @var bool
     */
    public $visibleFilterRow = true;
    /**
     * @var bool
     */
    public $visibleActionButtons = false;

    /**
     * AttendanceGrid constructor.
     * @param AttendanceServiceInterface $attendanceService
     * @param array $config
     */
    public function __construct(AttendanceServiceInterface $attendanceService, array $config = [])
    {
        $this->attendanceService = $attendanceService;
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->filterModel = AttendanceFilter::ensure($this->filterModel, 'search', $this->formData);
        $this->filterModel->patientId = $this->patientId;
        $this->dataProvider = $this->attendanceService->getAttendanceList($this->filterModel);
        $this->actionButtons['create'] = [
            'class' => LinkActionButton::class,
            'url' => ['/medical/ui/attendance/view', 'scenario' => ActiveRecord::SCENARIO_CREATE],
            'isDynamicModel' => false,
            'isAjax' => false,
            'disabled' => false,
            'value' => '',
            'options' => [
                'class' => 'btn btn-xs btn-primary',
                'icon' => 'plus',
            ],
        ];
        $this->actionButtons['delete'] = [
            'class' => LinkActionButton::class,
            'url' => ['/medical/rest/attendance/delete'],
            'isDynamicModel' => true,
            'isAjax' => true,
            'disabled' => true,
            'isConfirm' => true,
            'afterUpdateBlock' => $this,
            'value' => '',
            'options' => [
                'class' => 'btn btn-xs btn-danger',
                'icon' => 'remove',
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
            [
                'attribute' => 'cabinet.number',
                'value' => function (Attendance $model) {
                    if (!$model->cabinet instanceof Cabinet) {
                        return '';
                    }
                    return $model->cabinet->number;
                },
                'filter' => function () {
                    return Html::activeTextInput($this->filterModel, 'cabinetNumber', ['class' => 'form-control']);
                },
                'format' => 'html',
                'options' => [
                    'class' => 'col-xs-2'
                ],
            ],
            [
                'attribute' => 'employee.fullName',
                'value' => function (Attendance $model) {
                    $result = '';
                    if ($model->employee instanceof Employee) {
                        $result = $model->employee->fullName;
//                        if ($model->employee->speciality instanceof Speciality) {
//                            $result .= ' (' . $model->employee->speciality->title . ')';
//                        }
                    }
                    return $result;
                },
                'filter' => function () {
                    return Select2::widget([
                        'model' => $this->filterModel,
                        'attribute' => 'employeeId',
                        'data' => ArrayHelper::map(Employee::find()->notDeleted()->all(), 'id', 'fullName'),
                        'options' => [
                            'id' => UniqueKey::generate('employeeId'),
                            'placeholder' => \Yii::t('app', 'Select value...'),
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ]
                    ]);
                }
            ],
            [
                'attribute' => 'ehr.number',
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
        ];
        parent::init();
    }
}
