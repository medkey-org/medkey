<?php
namespace app\modules\security\port\rest\controllers;

use app\common\rest\Controller;
use app\common\filters\QueryParamAuth;
use app\common\widgets\ActiveForm;
use app\modules\security\application\UserServiceInterface;
use app\modules\security\models\form\UserPassword;
use yii\base\Module;
use app\modules\security\models\form\User as UserForm;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

/**
 * Class UserController
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class UserController extends Controller
{
    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'authenticator' => [
                'class' => QueryParamAuth::class,
                'isSession' => false,
                'optional' => [
                    '*',
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'except' => ['get-access-token-by-login-and-password'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function () {
                    throw new ForbiddenHttpException(\Yii::t('yii', 'You are not allowed to perform this action.'));
                }
            ],
        ]);
    }

    /**
     * UserController constructor.
     * @param string $id
     * @param UserServiceInterface $userManager
     * @param Module $module
     * @param array $config
     */
    public function __construct($id, Module $module, UserServiceInterface $userManager, array $config = [])
    {
        $this->userService = $userManager;
        parent::__construct($id, $module, $config);
    }

    public function actionValidateCreate()
    {
        $form = new UserForm([
            'scenario' => 'create'
        ]);
        $form->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson(ActiveForm::validate($form));
    }

    public function actionCreate()
    {
        $userForm = new UserForm([
            'scenario' => 'create',
        ]);
        $userForm->load(\Yii::$app->request->getBodyParams());
        return $this->asJson($this->userService->createUser($userForm));
    }

    public function actionValidateUpdate($id)
    {
        $userForm = new UserForm([
            'scenario' => 'update',
        ]);
        $userForm->id = $id;
        $userForm->load(\Yii::$app->request->getBodyParams());
        return $this->asJson(ActiveForm::validate($userForm));
    }

    public function actionUpdate($id)
    {
        $userForm = new UserForm([
            'scenario' => 'update',
        ]);
        $userForm->id = $id;
        $userForm->load(\Yii::$app->request->getBodyParams());
        return $this->asJson($this->userService->updateUser($userForm));
    }

    public function actionChangePasswordFromUserCard()
    {
        $form = new UserPassword();
        $form->load(\Yii::$app->request->getBodyParams());
        $this->userService->changePasswordFromUserCard($form);
    }

    public function actionGetAccessTokenByLoginAndPassword($login = null, $password = null)
    {
        return $this->asJson($this->userService->getAccessTokenByLoginAndPassword($login, $password));
    }
}
