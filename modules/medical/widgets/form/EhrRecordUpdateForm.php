<?php
namespace app\modules\medical\widgets\form;

use app\common\db\ActiveRecord;
use app\common\widgets\FormWidget;
use app\common\wrappers\DynamicModal;
use app\modules\medical\models\orm\Ehr;
use app\modules\medical\models\orm\EhrRecord;

/**
 * Class EhrRecordUpdateForm
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class EhrRecordUpdateForm extends EhrRecordCreateForm
{
    /**
     * @var EhrRecord
     */
    public $model;


    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->model = EhrRecord::ensure($this->model, ActiveRecord::SCENARIO_UPDATE);
        $this->model->ehr_id = $this->ehrId;
        $this->action = ['/medical/rest/ehr-record/update', 'id' => $this->model->id];
        $this->validationUrl = ['/medical/rest/ehr-record/validate-update', 'id' => $this->model->id];
        FormWidget::init();
    }

    /**
     * @inheritdoc
     */
    public function wrapperOptions()
    {
        return [
            'header' => 'Редактирование записи',
            'wrapperClass' => DynamicModal::class,
            'size' => 'modal-lg',
        ];
    }
}
