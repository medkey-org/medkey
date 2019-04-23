<?php
namespace app\modules\security\widgets\form;

use app\common\db\ActiveRecord;
use app\common\helpers\Html;
use app\common\widgets\FormWidget;
use app\common\wrappers\DynamicModal;
use app\modules\security\models\orm\AclRole;
use app\modules\security\SecurityModule;

/**
 * Class AccessRoleCreateForm
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class AccessRoleCreateForm extends FormWidget
{
    /**
     * @var AclRole
     */
    public $model;
    /**
     * @var string|array
     */
    public $action = ['/security/ui/acl-role/create'];
    /**
     * @var string|array
     */
    public $validationUrl = ['/security/ui/acl-role/validate-create'];


    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->model = AclRole::ensureWeak($this->model, ActiveRecord::SCENARIO_CREATE);
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function renderForm($model, $form)
    {
        echo $form->field($model, 'name');
        echo $form->field($model, 'short_name');
        echo $form->field($model, 'description');
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
            'header' => SecurityModule::t('role', 'Create role'),
        ];
    }
}
