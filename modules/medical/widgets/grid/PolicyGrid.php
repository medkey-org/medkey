<?php
namespace app\modules\medical\widgets\grid;

use app\common\button\WidgetLoaderButton;
use app\common\grid\GridView;
use app\common\helpers\CommonHelper;
use app\modules\medical\application\PolicyServiceInterface;
use app\modules\medical\models\finders\PolicyFilter;
use app\modules\medical\models\orm\Policy;
use app\modules\medical\widgets\form\PolicyCreateForm;
use app\modules\medical\widgets\form\PolicyUpdateForm;

/**
 * Class PolicyGrid
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class PolicyGrid extends GridView
{
    /**
     * @var PolicyFilter
     */
    public $filterModel;
    /**
     * @var PolicyServiceInterface
     */
    public $policyService;
    /**
     * @var string
     */
    public $patientId;


    /**
     * PolicyGrid constructor.
     * @param PolicyServiceInterface $policyService
     * @param array $config
     */
    public function __construct(PolicyServiceInterface $policyService, array $config = [])
    {
        $this->policyService = $policyService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->filterModel = PolicyFilter::ensure($this->filterModel);
        $this->filterModel->patientId = $this->patientId;
        $this->dataProvider = $this->policyService->getPolicyList($this->filterModel);
        $this->actionButtons['create'] = [
            'disabled' => false,
            'class' => WidgetLoaderButton::class,
            'widgetClass' => PolicyCreateForm::class,
            'widgetConfig' => [
                'patientId' => $this->patientId,
                'afterUpdateBlockId' => $this->getId(),
            ],
            'isDynamicModel' => false,
            'value' => '',
            'options' => [
                'class' => 'btn btn-xs btn-primary',
                'icon' => 'plus',
            ],
        ];
        $this->actionButtons['update'] = [
            'disabled' => true,
            'class' => WidgetLoaderButton::class,
            'widgetClass' => PolicyUpdateForm::class,
            'widgetConfig' => [
                'afterUpdateBlockId' => $this->getId(),
            ],
            'isDynamicModel' => true,
            'value' => '',
            'options' => [
                'class' => 'btn btn-xs btn-primary',
                'icon' => 'edit',
            ],
        ];
        $this->columns = [
            'number',
//            'series',
            [
                'attribute' => 'type',
                'value' => function (Policy $model) {
                    return $model->getTypeName();
                }
            ],
            [
                'attribute' => 'issue_date',
                'value' => function (Policy $model) {
                    if (empty($model->issue_date)) {
                        return '';
                    }
                    return \Yii::$app->formatter->asDate($model->issue_date, CommonHelper::FORMAT_DATE_UI);
                }
            ],
            [
                'attribute' => 'expiration_date',
                'value' => function (Policy $model) {
                    if (empty($model->expiration_date)) {
                        return '';
                    }
                    return \Yii::$app->formatter->asDate($model->expiration_date, CommonHelper::FORMAT_DATE_UI);
                }
            ],
        ];
        return parent::init();
    }
}
