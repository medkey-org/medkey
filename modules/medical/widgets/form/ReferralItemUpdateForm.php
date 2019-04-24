<?php
namespace app\modules\medical\widgets\form;
use app\common\db\ActiveRecord;
use app\common\widgets\FormWidget;
use app\common\wrappers\DynamicModal;
use app\modules\medical\MedicalModule;
use app\modules\medical\models\orm\ReferralItem;

/**
 * Class ReferralItemUpdateForm
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ReferralItemUpdateForm extends ReferralItemCreateForm
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->model = ReferralItem::ensure($this->model, ActiveRecord::SCENARIO_UPDATE);
        $this->action = ['/medical/rest/referral-item/update', 'id' => $this->model->id];
        $this->validationUrl = ['/medical/rest/referral-item/validate-update', 'id' => $this->model->id];
        FormWidget::init();
    }

    /**
     * @inheritdoc
     */
    public function wrapperOptions()
    {
        return [
            'header' => MedicalModule::t('referral', 'Edit referral item'),
            'wrapperClass' => DynamicModal::class
        ];
    }
}
