<?php
namespace app\modules\config\widgets\grid;

use app\common\button\LinkActionButton;
use app\common\button\WidgetLoaderButton;
use app\common\grid\GridView;
use app\modules\config\application\WorkflowServiceInterface;
use app\modules\config\application\WorkflowTransitionServiceInterface;
use app\modules\config\models\finders\WorkflowTransitionFinder;
use app\modules\config\models\orm\Workflow;
use app\modules\config\models\orm\WorkflowTransition;
use app\modules\config\widgets\form\WorkflowTransitionCreateForm;
use app\modules\config\widgets\form\WorkflowTransitionUpdateForm;

/**
 * Class WorkflowTransitionGrid
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class WorkflowTransitionGrid extends GridView
{
    public $filterModel;
    public $workflowId;
    public $workflowTransitionService;
    public $workflowService;

    public function __construct(WorkflowServiceInterface $workflowService, WorkflowTransitionServiceInterface $workflowTransitionService, array $config = [])
    {
        $this->workflowTransitionService = $workflowTransitionService;
        $this->workflowService = $workflowService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->filterModel = WorkflowTransitionFinder::ensure($this->filterModel, 'search', $this->formData);
        $this->filterModel->workflowId = $this->workflowId;
        $this->dataProvider = $this->workflowTransitionService->getWorkflowTransitionList($this->filterModel);
        $workflow = $this->workflowService-> getWorkflowById($this->workflowId);
        if ($workflow->status === Workflow::STATUS_UPDATING) {
            $this->actionButtons['create'] = [
                'class' => WidgetLoaderButton::class,
                'widgetClass' => WorkflowTransitionCreateForm::class,
                'disabled' => false,
                'isDynamicModel' => false,
                'value' => '',
                'widgetConfig' => [
                    'afterUpdateBlockId' => $this->getId(),
                    'workflowId' => $this->workflowId
                ],
                'options' => [
                    'class' => 'btn btn-primary btn-xs',
                    'icon' => 'plus'
                ]
            ];
            $this->actionButtons['update'] = [
                'class' => WidgetLoaderButton::class,
                'widgetClass' => WorkflowTransitionUpdateForm::class,
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
            $this->actionButtons['delete'] = [
                'class' => LinkActionButton::class,
                'url' => ['/config/rest/workflow-transition/delete'],
                'isDynamicModel' => true,
                'isAjax' => true,
                'disabled' => true,
                'isConfirm' => true,
                'afterUpdateBlock' => $this,
                'value' => '',
                'options' => [
                    'class' => 'btn btn-danger btn-xs',
                    'icon' => 'remove',
                ],
            ];
        }
        $this->columns = [
            'name',
            [
                'attribute' => 'from_id',
                'value' => function (WorkflowTransition $model) {
                    return $model->statusFrom->state_alias . ' (' . $model->statusFrom->state_value . ')';
                }
            ],
            [
                'attribute' => 'to_id',
                'value' => function (WorkflowTransition $model) {
                    return $model->statusTo->state_alias . ' (' . $model->statusTo->state_value . ')';
                }
            ],
        ];
        parent::init();
    }
}
