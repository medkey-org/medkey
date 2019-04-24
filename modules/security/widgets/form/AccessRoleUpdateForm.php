<?php
namespace app\modules\security\widgets\form;

use app\common\db\ActiveRecord;
use app\common\widgets\FormWidget;
use app\common\wrappers\DynamicModal;
use app\modules\security\models\orm\AclRole;
use app\modules\security\SecurityModule;

/**
 * Class AccessRoleUpdateForm
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class AccessRoleUpdateForm extends AccessRoleCreateForm
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->model = AclRole::ensure($this->model, ActiveRecord::SCENARIO_UPDATE);
        $this->action = ['/security/ui/acl-role/update', 'id' => $this->model->id];
        $this->validationUrl = ['/security/ui/acl-role/update', 'id' => $this->model->id];
        FormWidget::init();
    }

    /**
     * @inheritdoc
     */
    public function wrapperOptions()
    {
        return [
            'wrapperClass' => DynamicModal::class,
            'header' => SecurityModule::t('role', 'Edit role'),
        ];
    }
}
