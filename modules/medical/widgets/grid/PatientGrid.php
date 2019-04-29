<?php
namespace app\modules\medical\widgets\grid;

use app\common\button\LinkActionButton;
use app\common\db\ActiveRecord;
use app\common\grid\GridView;
use app\common\helpers\CommonHelper;
use app\common\helpers\Html;
use app\modules\medical\models\finders\PatientFilter;
use app\modules\medical\models\orm\Patient;
use app\modules\medical\application\PatientServiceInterface;

/**
 * Class PatientGrid
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class PatientGrid extends GridView
{
    /**
     * @var PatientFilter
     */
    public $filterModel;
    /**
     * @var PatientServiceInterface
     */
    public $patientService;
    /**
     * @var bool
     */
    public $visibleFilterRow = true;


    /**
     * PatientGrid constructor.
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
        $this->filterModel = PatientFilter::ensure($this->filterModel, 'search', $this->formData);
        $this->dataProvider = $this->patientService->getPatientList($this->filterModel);
        $this->actionButtons['create'] = [
            'class' => LinkActionButton::class,
            'url' => ['/medical/ui/patient/view', 'scenario' => ActiveRecord::SCENARIO_CREATE],
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
//            'url' => ['/medical/rest/patient/delete'],
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
                'attribute' => 'fullName',
                'value' => function (Patient $model) {
                    return Html::a($model->fullName, ['/medical/ui/patient/view', 'id' => $model->id]);
                },
                'format' => 'html',
                'filter' => function () {
                    return Html::activeTextInput($this->filterModel, 'fullName', ['class' => 'form-control']);
                }
            ],
            [
                'attribute' => 'birthday',
                'value' => function (Patient $model) {
                    return \Yii::$app->formatter->asDate($model->birthday, CommonHelper::FORMAT_DATE_UI);
                },
                'filter' => function () {
                    return Html::activeDateInput($this->filterModel, 'birthday', [
                        'startAfterNow' => false,
                        'pluginOptions' => [
                            'todayHighlight' => false,
                        ],
                    ]);
                }
            ],
            [
                'attribute' => 'snils',
                'filter' => function () {
                    return Html::activeTextInput($this->filterModel, 'snils', ['class' => 'form-control']);
                }
            ],
        ];
        parent::init();
    }
}
