<?php
namespace app\modules\security\widgets\form;

use app\common\helpers\Html;
use app\common\widgets\FormWidget;
use app\common\wrappers\DynamicModal;
use app\modules\security\models\form\UserPassword;
use app\modules\security\SecurityModule;

/**
 * Class UserPasswordChangeForm
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class UserPasswordChangeForm extends FormWidget
{
    /**
     * @var int|string
     */
    public $userId;
    /**
     * @var bool
     */
    public $enableAjaxValidation = false;
    /**
     * @var array
     */
    public $action = ['/security/rest/user/change-password-from-user-card'];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->model = new UserPassword();
        $this->model->userId = $this->userId;
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function renderForm($model, $form)
    {
        echo $form->field($model, 'userId')->hiddenInput();
        echo $form->field($model, 'password')->passwordInput();
        echo $form->field($model, 'passwordRepeat')->passwordInput();
        echo Html::submitButton(\Yii::t('app', 'Save'), [
            'class' => 'btn btn-primary',
            'icon'  => 'saved'
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
            'header' => SecurityModule::t('user', 'Change password'),
            'wrapperClass' => DynamicModal::class,
        ];
    }
}
