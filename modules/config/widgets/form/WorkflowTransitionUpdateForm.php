<?php
namespace app\modules\config\widgets\form;

use app\common\helpers\Html;
use app\common\widgets\FormWidget;
use app\common\workflow\HandlerManagerInterface;
use app\common\wrappers\DynamicModal;
use app\modules\config\application\WorkflowServiceInterface;
use app\modules\config\application\WorkflowTransitionServiceInterface;
use app\modules\config\models\orm\WorkflowStatus;
use app\modules\config\ConfigModule;

/**
 * Class WorkflowTransitionUpdateForm
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class WorkflowTransitionUpdateForm extends FormWidget
{
    public $model;
    public $workflowTransitionService;

    public function __construct(WorkflowTransitionServiceInterface $workflowTransitionService, array $config = [])
    {
        $this->workflowTransitionService = $workflowTransitionService;
        parent::__construct($config);
    }

    public function init()
    {
        $this->model = $this->workflowTransitionService->getWorkflowTransitionForm($this->model);
        $this->validationUrl = ['/config/rest/workflow-transition/validate-update', 'id' => $this->model->id];
        $this->action = ['/config/rest/workflow-transition/update', 'id' => $this->model->id];
        $wf = \Yii::$container->get(WorkflowServiceInterface::class)->getWorkflowById($this->model->workflow_id);
        $this->model->workflow_module = $wf->orm_module;
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function renderForm($model, $form)
    {
        echo $form->field($model, 'workflow_id')->hiddenInput();
        echo $form->field($model, 'workflow_module')->hiddenInput();
        echo $form->field($model, 'name');
        echo $form->field($model, 'from_id')
            ->select2(WorkflowStatus::listAll(null, 'state_alias', 'id', ['status' => WorkflowStatus::STATUS_ACTIVE]));
        echo $form->field($model, 'to_id')
            ->select2(WorkflowStatus::listAll(null, 'state_alias', 'id', ['status' => WorkflowStatus::STATUS_ACTIVE]));
        echo $form
            ->field($model, 'handler_type')
            ->select2(\Yii::$container->get(HandlerManagerInterface::class)->registry($this->model->workflow_module, false));
        echo $form
            ->field($model, 'handler_method')
            ->select2(!empty($model->handler_type) ? \Yii::$container
                ->get(HandlerManagerInterface::class)
                ->registryMethods($model->workflow_module, $model->handler_type) : [], [
                'disabled' => !empty($model->handler_type) ? false : true,
            ]);
        echo $form->field($model, 'middleware')
            ->checkbox();
        echo Html::submitButton(\Yii::t('app', 'Save'), [
            'class' => 'btn btn-primary',
            'icon' => 'save'
        ]);
        echo '&nbsp';
        echo Html::button(\Yii::t('app', 'Cancel'), [
            'class' => 'btn btn-default',
            'data-dismiss' => 'modal'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function wrapperOptions()
    {
        return [
            'wrapperClass' => DynamicModal::class,
            'header' => ConfigModule::t('workflow', 'Update workflow transition'),
        ];
    }
}
