<?php
namespace app\modules\config\widgets\form;

use app\common\helpers\Html;
use app\common\widgets\FormWidget;
use app\common\workflow\HandlerManagerInterface;
use app\common\wrappers\DynamicModal;
use app\modules\config\application\WorkflowServiceInterface;
use app\modules\config\models\form\WorkflowTransition;
use app\modules\config\models\orm\WorkflowStatus;
use app\modules\config\ConfigModule;

/**
 * Class WorkflowTransitionCreateForm
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class WorkflowTransitionCreateForm extends FormWidget
{
    public $workflowId;
    public $model;
    public $validationUrl = ['/config/rest/workflow-transition/validate-create'];
    public $action = ['/config/rest/workflow-transition/create'];

    public function init()
    {
        $this->model = WorkflowTransition::ensure($this->model);
        $this->model->workflow_id = $this->workflowId;
        $wf = \Yii::$container->get(WorkflowServiceInterface::class)->getWorkflowById($this->workflowId);
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
            ->select2([], [
                'disabled' => true,
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
            'header' => ConfigModule::t('workflow', 'Create workflow transition'),
        ];
    }
}
