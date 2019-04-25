<?php
namespace app\modules\config\widgets\form;

use app\common\db\ActiveRecordRegistry;
use app\common\helpers\Html;
use app\common\widgets\FormWidget;
use app\common\wrappers\DynamicModal;
use app\modules\config\application\WorkflowServiceInterface;
use app\modules\config\models\orm\Workflow as WorkflowORM;
use app\modules\config\ConfigModule;

/**
 * Class WorkflowUpdateForm
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class WorkflowUpdateForm extends FormWidget
{
    public $model;
    public $workflowService;

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
        $this->model = $this->workflowService->getWorkflowForm($this->model);
        $this->validationUrl = ['/config/rest/workflow/validate-update', 'id' => $this->model->id];
        $this->action = ['/config/rest/workflow/update', 'id' => $this->model->id];
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
        echo $form->field($model, 'name');
        echo $form->field($model, 'status')
            ->select2(WorkflowORM::statuses());
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
            'header' => ConfigModule::t('workflow', 'Update workflow'),
        ];
    }
}
