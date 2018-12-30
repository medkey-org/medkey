<?php
namespace app\modules\security\port\ui\controllers;

use app\common\db\ActiveRecord;
use app\common\helpers\Url;
use app\common\web\Controller;
use app\common\widgets\ActiveForm;
use app\modules\security\application\UserServiceInterface;
use app\modules\security\models\form\LoginForm;
use app\modules\security\models\orm\User;
use yii\filters\AccessControl;
use yii\web\HttpException;
use yii\web\UnauthorizedHttpException;

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

    public function init()
    {
        parent::init();
        if (getenv('HTTP_BASIC_AUTH')) {
            $this->attachBehavior('http_auth', [
                'class' => \yii\filters\auth\HttpBasicAuth::class,
                'only' => ['login-form'],
                'auth' => function ($username, $password) {
                    $isLogout = \Yii::$app->session->getFlash('http_auth_logout');
                    if ($isLogout) {
                        \Yii::$app->response->getHeaders()->set('WWW-Authenticate', 'Basic realm="api"');
                        throw new UnauthorizedHttpException();
                    }
                    $user = User::find()
                        ->where(['login' => $username])
                        ->one();
                    if (!$user) {
                        \Yii::$app->response->getHeaders()->set('WWW-Authenticate', 'Basic realm="api"');
                        throw new UnauthorizedHttpException();
                    }
                    $isValid = \Yii::$app->getSecurity()->validatePassword($password, $user->password_hash);
                    if ($isValid) {
                        return $user;
                    }
                    \Yii::$app->response->getHeaders()->set('WWW-Authenticate', 'Basic realm="api"');
                    throw new UnauthorizedHttpException();
                }
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'except' => ['login-form', 'redirect-after-logout'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login', 'validate-login'],
                        'roles' => ['?']
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ],
                ],
            ],
        ];
    }

    public function __construct($id, $module, UserServiceInterface $userManager, array $config = [])
    {
        $this->userService = $userManager;
        parent::__construct($id, $module, $config);
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @param string|int $id
     * @return string
     */
    public function actionView($id = null)
    {
        /** @var $modelClass ActiveRecord */
        return $this->render('view', [
            'model' => $id
        ]);
    }

    public function actionLoginForm()
    {
        if (!\Yii::$app->getUser()->getIsGuest()) {
            return $this->goHome();
        }
        $this->layout = '@app/themes/basic/layouts/empty';
        return $this->render('login');
    }

    public function actionRedirectAfterLogout()
    {
        \Yii::$app->session->setFlash('http_auth_logout');
        $this->redirect(['/security/ui/user/login-form']);
    }

    public function actionValidateLogin()
    {
        $model = new LoginForm();
        $model->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson(ActiveForm::validate($model));
    }

    public function actionLogin()
    {
        $model = new LoginForm();
        if (
            $model->load(\Yii::$app->request->post())
            && $model->validate()
            && $this->userService->login($model->identity, 36000)
        ) {
            return $this->goBack();
        }
        throw new HttpException(401, 'Auth is not valid.');
    }

    public function actionLogout()
    {
        if(\Yii::$app->user->logout()) {
            if (getenv('HTTP_BASIC_AUTH')) {
                return $this->redirect(Url::basicLogout(['/security/ui/user/redirect-after-logout']));
            } else {
                return $this->redirect(['/security/ui/user/login-form']);
            }
        }
        return $this->redirect(\Yii::$app->getUser()->loginUrl);
    }
}
