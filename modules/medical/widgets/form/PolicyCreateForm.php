<?php
namespace app\modules\medical\widgets\form;

use app\common\helpers\Html;
use app\common\widgets\FormWidget;
use app\common\wrappers\DynamicModal;
use app\modules\medical\application\PolicyServiceInterface;
use app\modules\medical\MedicalModule;
use app\modules\medical\models\form\Policy;
use app\modules\medical\models\orm\Insurance;

/**
 * Class PolicyCreateForm
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class PolicyCreateForm extends FormWidget
{
    /**
     * @var Policy
     */
    public $model;
    /**
     * @var string
     */
    public $patientId;
    /**
     * @var array
     */
    public $action = ['/medical/rest/policy/create'];
    /**
     * @var array
     */
    public $validationUrl = ['/medical/rest/policy/validate-create'];
    /**
     * @var PolicyServiceInterface
     */
    public $policyService;


    /**
     * PolicyCreateForm constructor.
     * @param PolicyServiceInterface $policyService
     * @param array $config
     */
    public function __construct(PolicyServiceInterface $policyService, array $config = [])
    {
        $this->policyService = $policyService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->model = $this->policyService->getPolicyForm($this->model);
        $this->model->patient_id = $this->patientId;
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function renderForm($model, $form)
    {
        echo $form->field($model, 'patient_id')->hiddenInput();
        echo $form->field($model, 'number');
        echo $form->field($model, 'issue_date')->dateInput();
        echo $form->field($model, 'expiration_date')->dateInput();
        echo $form->field($model, 'insurance_id')->select2(Insurance::listAll()); // todo через сервис
        echo $form->field($model, 'type')->select2(Policy::types());
        echo Html::submitButton(\Yii::t('app', 'Save'), [
            'class' => 'btn btn-primary',
            'icon' => 'plus',
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
            'header' => MedicalModule::t('policy', 'Add policy'),
            'wrapperClass' => DynamicModal::class,
        ];
    }
}
