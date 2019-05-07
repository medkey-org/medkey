<?php
namespace app\modules\medical\widgets\form;

use app\common\db\ActiveRecord;
use app\common\helpers\ArrayHelper;
use app\common\helpers\Html;
use app\common\widgets\FormWidget;
use app\common\wrappers\DynamicModal;
use app\modules\medical\MedicalModule;
use app\modules\medical\models\orm\Ehr;
use app\modules\medical\models\orm\EhrRecord;
use app\modules\organization\models\orm\Employee;

/**
 * Class EhrRecordCreateForm
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 * @deprecated
 */
class EhrRecordCreateForm extends FormWidget
{
    /**
     * @var EhrRecord
     */
    public $model;
    /**
     * @var Ehr
     */
    public $ehrId;
    /**
     * @var string
     */
    public $action = ['/medical/rest/ehr-record/create'];
    /**
     * @var string
     */
    public $validationUrl =  ['/medical/rest/ehr-record/validate-create'];


    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->model = EhrRecord::ensureWeak($this->model, ActiveRecord::SCENARIO_CREATE);
        $this->model->ehr_id = $this->ehrId;
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function renderForm($model, $form)
    {
//        echo $form->field($model, 'template')
//            ->textarea();
//        echo $form->field($model, 'type');
        echo $form->field($model, 'conclusion')
            ->textarea();
        echo $form->field($model, 'ehr_id')
            ->hiddenInput();
        echo $form->field($model, 'employee_id')
            ->select2(ArrayHelper::map(Employee::find()->notDeleted()->all(), 'id', function ($row) {
                return $row->last_name . ' ' . $row->first_name . ' ' . $row->middle_name;
            }));
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
            'header' => MedicalModule::t('ehr', 'Create EHR record'),
            'wrapperClass' => DynamicModal::class,
            'size' => 'modal-lg',
        ];
    }
}
