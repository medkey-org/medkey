<?php
namespace app\modules\medical\widgets\grid;

use app\common\base\UniqueKey;
use app\common\button\LinkActionButton;
use app\common\db\ActiveRecord;
use app\common\grid\GridView;
use app\common\helpers\ArrayHelper;
use app\common\helpers\Html;
use app\common\helpers\Url;
use app\modules\medical\models\finders\EhrFilter;
use app\modules\medical\models\orm\Ehr;
use app\modules\medical\models\orm\Patient;
use app\modules\medical\application\EhrServiceInterface;
use app\modules\medical\application\PatientServiceInterface;
use kartik\select2\Select2;
use yii\web\JsExpression;

/**
 * EHR registry grid
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class EhrGrid extends GridView
{
    /**
     * @var
     */
    public $filterModel;
    /**
     * @var Patient
     */
    public $patientId;
    /**
     * @var PatientServiceInterface
     */
    public $patientService;
    /**
     * @var EhrServiceInterface
     */
    public $ehrService;
    /**
     * @var bool
     */
    public $visibleFilterRow = true;


    /**
     * EhrGrid constructor.
     * @param PatientServiceInterface $patientService
     * @param EhrServiceInterface $ehrService
     * @param array $config
     */
    public function __construct(PatientServiceInterface $patientService, EhrServiceInterface $ehrService, array $config = [])
    {
        $this->patientService = $patientService;
        $this->ehrService = $ehrService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->filterModel = EhrFilter::ensure($this->filterModel, 'search', $this->formData);

        if (!empty($this->patientId)) {
            $this->filterModel->patientId = $this->patientId;
        }

        $this->dataProvider = $this->ehrService->getEhrList($this->filterModel);
        $this->actionButtons['create'] = [
            'class' => LinkActionButton::class,
            'url' => ['/medical/ui/ehr/view', 'patientId' => $this->patientId, 'scenario' => ActiveRecord::SCENARIO_CREATE],
            'isDynamicModel' => false,
            'isAjax' => false,
            'disabled' => false,
            'value' => '',
            'options' => [
                'class' => 'btn btn-xs btn-primary',
                'icon' => 'plus',
            ],
        ];
//        $this->actionButtons['delete'] = [
//            'class' => LinkActionButton::class,
//            'url' => ['/medical/rest/ehr/delete'],
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
        $this->columns = [
            [
                'attribute' => 'number',
                'format' => 'html',
                'value' => function (Ehr $model) {
                    return Html::a($model->number, ['/medical/ui/ehr/view', 'id' => $model->id]);
                },
                'options' => [
                    'class' => 'col-md-2',
                ],
            ],
            [
                'attribute' => 'type',
                'format' => 'html',
                'value' => function (Ehr $model) {
                    return $model->getTypeName();
                },
                'filter' => function () {
                    return Html::activeSelect2Input(
                        $this->filterModel,
                        'type',
                        Ehr::types(),
                        [
                            'class' => 'form-control',
                            'empty' => true,
                            'placeholder' => \Yii::t('app', 'Select value...'),
                        ],
                        [
                            'allowClear' => true,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return '" . \Yii::t('app', 'Nothing found') . "'; }"),
                            ],
                        ]
                    );
                },
            ],
            [
                'attribute' => 'patient_id',
                'format' => 'html',
                'value' => function (Ehr $model) {
                    if (!$model->patient instanceof Patient) {
                        return '';
                    }
                    return Html::a($model->patient->getFullName(), ['/medical/ui/patient/view', 'id' => $model->patient->id]);
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
                                'errorLoading' => new JsExpression("function () { return '" . \Yii::t('app', 'Nothing found') . "'; }"),
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
                },
            ]
        ];
        parent::init();
    }
}
