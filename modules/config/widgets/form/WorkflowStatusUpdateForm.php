<?php
namespace app\modules\config\widgets\form;

use app\common\helpers\Html;
use app\common\widgets\FormWidget;
use app\common\wrappers\DynamicModal;
use app\modules\config\application\WorkflowStatusServiceInterface;
use app\modules\config\models\orm\WorkflowStatus as WorkflowStatusORM;
use app\modules\config\ConfigModule;

/**
 * Class WorkflowStatusUpdateForm
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class WorkflowStatusUpdateForm extends FormWidget
{
    public $model;
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
        $this->model = $this->workflowStatusService->getWorkflowStatusForm($this->model);
        $this->validationUrl = ['/config/rest/workflow-status/validate-update', 'id' => $this->model->id];
        $this->action = ['/config/rest/workflow-status/update', 'id' => $this->model->id];
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function renderForm($model, $form)
    {
        echo $form->field($model, 'orm_module')->textInput([
            'readonly' => true,
            'style' => 'pointer-events: none;',
        ]);
        echo $form->field($model, 'orm_class')->textInput([
            'readonly' => true,
            'style' => 'pointer-events: none;',
        ]);
        echo $form->field($model, 'state_attribute')->hiddenInput();
        echo $form->field($model, 'state_value')->textInput([
            'readonly' => true,
            'style' => 'pointer-events: none;',
        ]); // todo порядковый NUMBER по orm_module/orm_entity
        echo $form->field($model, 'state_alias');
        echo $form->field($model, 'status')
            ->select2(WorkflowStatusORM::statuses());
        if ($model->is_start === false) { // todo booleanSelect2
            $model->is_start = 0;
        } elseif ($model->is_start === true) {
            $model->is_start = 1;
        }
        echo $form->field($model, 'is_start')->select2([
            0 => 'Нет',
            1 => 'Да',
        ]);
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
            'header' => ConfigModule::t('workflow', 'Update workflow status'),
        ];
    }
}
