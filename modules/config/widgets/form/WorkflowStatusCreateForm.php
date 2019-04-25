<?php
namespace app\modules\config\widgets\form;

use app\common\db\ActiveRecordRegistry;
use app\common\helpers\Html;
use app\common\widgets\FormWidget;
use app\common\wrappers\DynamicModal;
use app\modules\config\application\WorkflowStatusServiceInterface;
use app\modules\config\ConfigModule;
use app\modules\config\models\orm\WorkflowStatus as WorkflowStatusORM;

/**
 * Class WorkflowStatusCreateForm
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class WorkflowStatusCreateForm extends FormWidget
{
    public $model;
    public $validationUrl = ['/config/rest/workflow-status/validate-create'];
    public $action = ['/config/rest/workflow-status/create'];
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
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function renderForm($model, $form)
    {
        echo $form->field($model, 'orm_module')
            ->select2(\Yii::$app->getModuleAliases(['security', 'debug', 'gii', 'config', 'organization', 'location']));
        echo $form->field($model, 'orm_class')
            ->select2(!empty($model->orm_module) ? ActiveRecordRegistry::registry($model->orm_module) : [], [
                'disabled' => !empty($model->orm_module) ? false : true,
            ]);
        $model->state_attribute = WorkflowStatusORM::STATE_ATTRIBUTE_DEFAULT;
        echo $form->field($model, 'state_attribute')->hiddenInput();
        echo $form->field($model, 'state_value'); // todo порядковый NUMBER
        echo $form->field($model, 'state_alias');
        echo $form->field($model, 'status')
            ->select2(WorkflowStatusORM::statuses());
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
            'header' => ConfigModule::t('workflow', 'Create workflow status'),
        ];
    }
}
