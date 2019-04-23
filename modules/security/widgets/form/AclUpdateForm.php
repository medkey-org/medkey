<?php
namespace app\modules\security\widgets\form;

use app\common\db\ActiveRecord;
use app\common\widgets\FormWidget;
use app\common\wrappers\DynamicModal;
use app\modules\security\models\orm\Acl;
use app\modules\security\SecurityModule;

/**
 * Class AclRecordUpdateForm
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class AclUpdateForm extends AclCreateForm
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->model = Acl::ensure($this->model, ActiveRecord::SCENARIO_UPDATE);
        $this->action = ['/security/ui/acl/update', 'id' => $this->model->id];
        $this->validationUrl = ['/security/ui/acl/validate-update', 'id' => $this->model->id];
        FormWidget::init();
    }

    /**
     * @inheritdoc
     */
    public function wrapperOptions()
    {
        return [
            'wrapperClass' => DynamicModal::class,
            'header' => SecurityModule::t('acl', 'Update ACL record'),
        ];
    }
}
