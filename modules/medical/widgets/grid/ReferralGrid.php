<?php
namespace app\modules\medical\widgets\grid;

use app\common\base\UniqueKey;
use app\common\button\LinkActionButton;
use app\common\button\WidgetLoaderButton;
use app\common\db\ActiveRecord;
use app\common\grid\GridView;
use app\common\helpers\ArrayHelper;
use app\common\helpers\Html;
use app\common\helpers\Url;
use app\modules\crm\models\orm\Order;
use app\modules\medical\models\finders\ReferralFilter;
use app\modules\medical\models\orm\Ehr;
use app\modules\medical\models\orm\Patient;
use app\modules\medical\models\orm\Referral;
use app\modules\medical\application\EhrServiceInterface;
use app\modules\medical\application\PatientServiceInterface;
use app\modules\medical\application\ReferralServiceInterface;
use app\modules\medical\widgets\misc\ListMedworkerSchedule;
use kartik\select2\Select2;
use yii\web\JsExpression;

/**
 * Class ReferralGrid
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ReferralGrid extends GridView
{
    /**
     * @var ReferralFilter
     */
    public $filterModel;
    /**
     * @var Order
     */
    public $orderId;
    /**
     * @var Ehr
     */
    public $ehr;
    /**
     * @var ReferralServiceInterface
     */
    public $referralService;
    /**
     * @var bool
     */
    public $visibleFilterRow = true;


    /**
     * ReferralGrid constructor.
     * @param ReferralServiceInterface $referralService
     * @param array $config
     */
    public function __construct(ReferralServiceInterface $referralService, array $config = [])
    {
        $this->referralService = $referralService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->filterModel = ReferralFilter::ensure($this->filterModel, 'search', $this->formData);
        $this->ehr = Ehr::ensureWeak($this->ehr);
        empty($this->ehr->id) ?: $this->filterModel->ehrId = $this->ehr->id;
        empty($this->orderId) ?: $this->filterModel->orderId = $this->orderId;
        $this->dataProvider = $this->referralService->getReferralList($this->filterModel);
        $this->actionButtons['create'] = [
            'class' => LinkActionButton::class,
            'url' => ['/medical/ui/referral/view', 'scenario' => ActiveRecord::SCENARIO_CREATE, 'ehrId' => $this->ehr->id],
            'isDynamicModel' => false,
            'isAjax' => false,
            'disabled' => false,
            'value' => '',
            'options' => [
                'class' => 'btn btn-xs btn-primary',
                'icon' => 'plus',
            ],
        ];
        $this->actionButtons['record'] = [
            'class' => WidgetLoaderButton::class,
            'widgetClass' => ListMedworkerSchedule::class,
            'isDynamicModel' => true,
            'disabled' => true,
            'value' => '',
            'options' => [
                'class' => 'btn btn-primary btn-xs',
                'icon' => 'time'
            ]
        ];
//        $this->actionButtons['delete'] = [
//            'class' => LinkActionButton::class,
//            'url' => ['/medical/rest/referral/delete'],
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
                'value' => function (Referral $model) {
                    return Html::a($model->number, ['/medical/ui/referral/view/', 'id' => $model->id]);
                },
                'options' => [
                    'class' => 'col-md-2',
                ]
            ],
            [
                'attribute' => 'status',
                'value' => function (Referral $model) {
                    return $model->getStatusName();
                },
                'filter' => function () {
                    return Html::activeSelect2Input(
                        $this->filterModel,
                        'status',
                        Referral::statuses(),
                        [
                            'class' => 'form-control',
                            'empty' => true,
                            'placeholder' => \Yii::t('app', 'Select value...'),
                        ],
                        [
                            'allowClear' => true,
//                            'minimumInputLength' => 1,
                            'language' => [
                                'errorLoading' => new JsExpression("function () { return 'Ничего не найдено.'; }"),
                            ],
                        ]
                    );
                },
            ],
            [
                'attribute' => 'ehr_id',
                'value' => function (Referral $model) {
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
                        ]
                    ]);
                },
                'format' => 'html',
            ],
            [
                'attribute' => 'patient_id',
                'value' => function (Referral $model) {
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
        ];
        parent::init();
    }
}
