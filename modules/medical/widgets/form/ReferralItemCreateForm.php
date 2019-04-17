<?php
namespace app\modules\medical\widgets\form;

use app\common\db\ActiveRecord;
use app\common\helpers\Html;
use app\common\widgets\FormWidget;
use app\common\wrappers\DynamicModal;
use app\modules\medical\MedicalModule;
use app\modules\medical\models\orm\Referral;
use app\modules\medical\models\orm\ReferralItem;
use app\modules\medical\models\orm\Service;

/**
 * Class ReferralItemCreateForm
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ReferralItemCreateForm extends FormWidget
{
    /**
     * @var ReferralItem
     */
    public $model;
    /**
     * @var Referral
     */
    public $referral;
    /**
     * @var array
     */
    public $action = ['/medical/rest/referral-item/create'];
    /**
     * @var array
     */
    public $validationUrl = ['/medical/rest/referral-item/validate-create'];


    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->model = ReferralItem::ensureWeak($this->model, ActiveRecord::SCENARIO_CREATE);
        $this->referral = Referral::ensure($this->referral);
        $this->model->referral_id = $this->referral->id;
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function renderForm($model, $form)
    {
        echo $form->field($model, 'referral_id')
            ->hiddenInput();
        echo $form->field($model, 'service_id')
            ->select2(Service::listAll());
        echo Html::submitButton(\Yii::t('app','Save'), [
            'class' => 'btn btn-primary',
            'icon' => 'save'
        ]);
        echo '&nbsp';
        echo Html::button(\Yii::t('app','Cancel'), [
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
            'header' => MedicalModule::t('referral', 'Create referral item'),
            'wrapperClass' => DynamicModal::className(),
        ];
    }
}
