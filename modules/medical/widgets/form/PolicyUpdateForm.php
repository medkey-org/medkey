<?php
namespace app\modules\medical\widgets\form;
use app\common\widgets\FormWidget;
use app\common\wrappers\DynamicModal;
use app\modules\medical\models\form\Policy;
use app\modules\medical\MedicalModule;

/**
 * Class PolicyUpdateForm
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class PolicyUpdateForm extends PolicyCreateForm
{
    /**
     * @var Policy
     */
    public $model;


    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->model = $this->policyService->getPolicyForm($this->model);
        $this->action = ['/medical/rest/policy/update', 'id' => $this->model->id];
        $this->validationUrl = ['/medical/rest/policy/validate-update'];
        FormWidget::init();
    }

    /**
     * @inheritdoc
     */
    public function wrapperOptions()
    {
        return [
            'wrapperClass' => DynamicModal::class,
            'header' => MedicalModule::t('policy', 'Edit policy'),
        ];
    }
}
