<?php
namespace app\modules\config\widgets\grid;

use app\common\button\WidgetLoaderButton;
use app\common\grid\GridView;
use app\modules\config\application\WorkflowStatusServiceInterface;
use app\modules\config\models\finders\WorkflowStatusFinder;
use app\modules\config\models\orm\WorkflowStatus;
use app\modules\config\widgets\form\WorkflowStatusCreateForm;
use app\modules\config\widgets\form\WorkflowStatusUpdateForm;

/**
 * Class WorkflowStatusGrid
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class WorkflowStatusGrid extends GridView
{
    public $filterModel;
    public $workflowStatusService;

    public function __construct(WorkflowStatusServiceInterface $workflowStatusService, array $config = [])
    {
        $this->workflowStatusService = $workflowStatusService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->filterModel = WorkflowStatusFinder::ensure($this->filterModel, 'search', $this->formData);
        $this->dataProvider = $this->workflowStatusService->getWorkflowStatusList($this->filterModel);
        $this->columns = [
            'orm_module',
            'orm_class',
            'state_attribute',
            'state_value',
            'state_alias',
            [
                'attribute' => 'status',
                'value' => function (WorkflowStatus $model) {
                    return $model->getStatusName();
                }
            ],
            [
                'attribute' => 'is_start',
                'value' => function ($model) {
                    return $model->is_start ? 'Да' : 'Нет';
                }
            ],
        ];
        $this->actionButtons['create'] = [
            'class' => WidgetLoaderButton::class,
            'widgetClass' => WorkflowStatusCreateForm::class,
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
            'widgetClass' => WorkflowStatusUpdateForm::class,
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
