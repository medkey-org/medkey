<?php
namespace app\modules\security\widgets\form;

use app\common\db\ActiveRecord;
use app\common\helpers\Html;
use app\common\widgets\FormWidget;
use app\common\wrappers\DynamicModal;
use app\modules\security\models\orm\Acl;
use app\modules\security\models\orm\AclRole;
use app\modules\security\application\AclService;
use app\modules\security\SecurityModule;

/**
 * Class AclRecordCreateForm
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class AclCreateForm extends FormWidget
{
    /**
     * @var Acl
     */
    public $model;
    /**
     * @var array
     */
    public $action = ['/security/ui/acl/create'];
    /**
     * @var array
     */
    public $validationUrl = ['/security/ui/acl/validate-create'];


    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->model = Acl::ensureWeak($this->model, ActiveRecord::SCENARIO_CREATE);
        $this->model->type = Acl::TYPE_SERVICE;
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function renderForm($model, $form)
    {
        echo $form->field($model, 'type')
            ->select2(Acl::types());
        echo $form->field($model, 'acl_role_id')
            ->select2(AclRole::listAll(null, 'name'));
        echo $form->field($model, 'module')
            ->select2(\Yii::$app->getModuleAliases(['debug', 'gii']));
        echo $form->field($model, 'entity_type')
            ->select2(!empty($model->module) ? \Yii::$app->acl->resourceRegistry($model->module, $model->type, false) : [], [
                'disabled' => !empty($model->module) ? false : true,
            ]);
        echo $form->field($model, 'action')
            ->select2(
                !empty($model->module) && !empty($model->entity_type) ? \Yii::createObject(\Yii::$app->acl->getResourceClass($model->module, $model->entity_type, $model->type))->getPrivileges() : [], [
                'disabled' => !empty($model->module) && !empty($model->entity_type) ? false : true,
            ]);
//        echo $form->field($model, 'entity_id');
        echo $form->field($model, 'rule')
            ->select2(Acl::aclRules(), [
                'multiple' => true
            ]);
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
            'header' => SecurityModule::t('acl', 'Create ACL record'),
        ];
    }
}
