<?php
namespace app\modules\workplan\widgets\grid;

use app\common\button\WidgetLoaderButton;
use app\common\grid\GridView;
use app\common\helpers\CommonHelper;
use app\common\wrappers\DynamicModal;
use app\modules\organization\models\orm\Employee;
use app\modules\workplan\models\finders\WorkplanFilter;
use app\modules\workplan\application\WorkplanServiceInterface;
use app\modules\workplan\widgets\form\WorkplanCreateForm;
use app\modules\workplan\widgets\form\WorkplanUpdateForm;
use app\modules\workplan\WorkplanModule;

/**
 * Class WorkplanGrid
 * @package Module\Workplan
 * @copyright 2012-2019 Medkey
 */
class WorkplanGrid extends GridView
{
    /**
     * @var string
     */
    public $employeeId;
    /**
     * @var WorkplanFilter
     */
    public $filterModel;
    /**
     * @var WorkplanServiceInterface
     */
    public $workplanService;


    /**
     * WorkplanGrid constructor.
     * @param WorkplanServiceInterface $workplanService
     * @param array $config
     */
    public function __construct(WorkplanServiceInterface $workplanService, array $config = [])
    {
        $this->workplanService = $workplanService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->filterModel = WorkplanFilter::ensure($this->filterModel, 'search');
        $this->filterModel->employeeId = $this->employeeId;
        $this->dataProvider = $this->workplanService->getWorkplanList($this->filterModel);
        $this->columns = [
            [
                'attribute' => 'employee_id',
                'value' => function ($model) {
                    if (!$model->employee instanceof Employee) {
                        return '';
                    }
                    return $model->employee->fullName;
                }
            ],
            [
                'attribute' => 'since_date',
                'value' => function ($model) {
                    return \Yii::$app->formatter->asDate($model->since_date, CommonHelper::FORMAT_DATE_UI);
                }
            ],
            [
                'attribute' => 'expire_date',
                'value' => function ($model) {
                    return \Yii::$app->formatter->asDate($model->expire_date, CommonHelper::FORMAT_DATE_UI);
                }
            ],
            [
                'attribute' => 'since_time',
                'value' => function ($model) {
                    return \Yii::$app->formatter->asTime($model->since_time, CommonHelper::FORMAT_TIME_UI);
                }
            ],
            [
                'attribute' => 'expire_time',
                'value' => function ($model) {
                    return \Yii::$app->formatter->asTime($model->expire_time, CommonHelper::FORMAT_TIME_UI);
                }
            ],
        ];
        $this->actionButtons['create'] = [
            'disabled' => false,
            'class' => WidgetLoaderButton::class,
            'widgetClass' => WorkplanCreateForm::class,
            'widgetConfig' => [
                'employeeId' => $this->employeeId,
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
            'widgetClass' => WorkplanUpdateForm::class,
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
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function wrapperOptions()
    {
        return [
            'wrapperClass' => DynamicModal::class,
            'header' => WorkplanModule::t('workplan', 'Workplan list'),
        ];
    }
}
