<?php
namespace app\modules\config\widgets\grid;

use app\common\button\WidgetLoaderButton;
use app\common\grid\GridView;
use app\common\helpers\Html;
use app\common\helpers\Url;
use app\modules\config\application\WorkflowServiceInterface;
use app\modules\config\models\finders\WorkflowFinder;
use app\modules\config\models\orm\Workflow;
use app\modules\config\widgets\form\WorkflowCreateForm;
use app\modules\config\widgets\form\WorkflowUpdateForm;

/**
 * Class WorkflowGrid
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class WorkflowGrid extends GridView
{
    /**
     * @var WorkflowFinder
     */
    public $filterModel;
    /**
     * @var WorkflowServiceInterface
     */
    public $workflowService;

    /**
     * WorkflowGrid constructor.
     * @param WorkflowServiceInterface $workflowService
     * @param array $config
     */
    public function __construct(WorkflowServiceInterface $workflowService, array $config = [])
    {
        $this->workflowService = $workflowService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->filterModel = WorkflowFinder::ensure($this->filterModel, 'search');
        $this->dataProvider = $this->workflowService->getWorkflowList($this->filterModel);
        $this->columns = [
            [
                'attribute' => 'name',
                'value' => function (Workflow $model) {
                    return Html::a(Html::encode($model->name), Url::to(['/config/ui/workflow/view', 'id' => $model->id]));
                },
                'format' => 'html',
            ],
            'orm_module',
            'orm_class',
            [
                'attribute' => 'status',
                'value' => function (Workflow $model) {
                    return $model->getStatusName();
                }
            ],
        ];
        $this->actionButtons['create'] = [
            'class' => WidgetLoaderButton::class,
            'widgetClass' => WorkflowCreateForm::class,
            'disabled' => false,
            'isDynamicModel' => false,
            'value' => '',
            'widgetConfig' => [
                'afterUpdateBlockId' => $this->getId(),
            ],
            'options' => [
                'class' => 'btn btn-primary btn-xs',
                'icon' => 'plus'
            ]
        ];
        $this->actionButtons['update'] = [
            'class' => WidgetLoaderButton::class,
            'widgetClass' => WorkflowUpdateForm::class,
            'disabled' => true,
            'isDynamicModel' => true,
            'value' => '',
            'widgetConfig' => [
                'afterUpdateBlockId' => $this->getId(),
            ],
            'options' => [
                'class' => 'btn btn-primary btn-xs',
                'icon' => 'edit'
            ]
        ];
        parent::init();
    }
}
