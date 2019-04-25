<?php
namespace app\modules\security\widgets\form;

use app\common\helpers\Html;
use app\common\widgets\FormWidget;
use app\modules\security\models\form\LoginForm;
use app\modules\security\models\orm\User;
use app\modules\security\SecurityModule;

/**
 * Class UserLoginForm
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class UserLoginForm extends FormWidget
{
    /**
     * @var User
     */
    public $model;
    /**
     * @var array
     */
    public $action = ['/security/ui/user/login'];
    /**
     * @var array
     */
    public $validationUrl = ['/security/ui/user/validate-login'];
    /**
     * @var bool
     */
    public $ajaxSubmit = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->model = new LoginForm();
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function renderForm($model, $form)
    {
//        echo $form->errorSummary($model);
        echo $form->field($model, 'login', [
            'inputTemplate' => '<div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></span>{input}</div>',
        ]);
        echo $form->field($model, 'password', [
            'inputTemplate' => '<div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-lock" aria-hidden="true"></span></span>{input}</div>',

        ])->passwordInput([
            'autocomplete' => 'off',
        ]);
        echo Html::submitButton(SecurityModule::t('user', 'Login'), [
            'class' => 'btn btn-default btn-block',
        ]);
//        echo Html::a('Создать пользователя', Url::to(['/security/ui/user/view', 'scenario' => 'create']), [
//            'class' => 'btn btn-default',
//        ]);
    }
}
