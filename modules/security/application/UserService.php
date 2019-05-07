<?php
namespace app\modules\security\application;

use app\common\data\ActiveDataProvider;
use app\common\helpers\ArrayHelper;
use app\common\helpers\CommonHelper;
use app\common\helpers\Json;
use app\common\service\exception\AccessApplicationServiceException;
use app\common\service\exception\ApplicationServiceException;
use app\modules\security\models\finders\UserFinder;
use app\modules\security\models\orm\AclRole;
use app\modules\security\models\orm\User;
use app\modules\security\models\form\User as UserForm;
use app\common\service\ApplicationService;
use app\modules\security\SecurityModule;
use yii\web\IdentityInterface;

/**
 * Class UserService
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class UserService extends ApplicationService implements UserServiceInterface
{
    /**
     * @inheritdoc
     */
    public function aclAlias()
    {
        return SecurityModule::t('user', 'User');
    }

    /**
     * @inheritdoc
     */
    public function getPrivileges()
    {
        return [
            'getUserList' => SecurityModule::t('user','Get users list'),
            'createUser' => SecurityModule::t('user','Create user'),
            'getUserById' => SecurityModule::t('user','Get user card'),
            'updateUser' => SecurityModule::t('user','Update user'),
        ];
    }

    public function getUserById($id)
    {
        if (!$this->isAllowed('getUserById')) {
            throw new AccessApplicationServiceException(SecurityModule::t('user','Access to the users list restricted'));
        }
        return User::findOne($id);
    }

    /**
     * Can return empty string (necessary for default language handling)
     * @return string
     */
    public function getCurrentUserLanguage(): ?string
    {
        /** @var User $user */
        $user = \Yii::$app->user->identity;
        if ($user instanceof User && (!empty(trim($user->language)))) {
            return $user->language;
        }
        return '';
    }

    /**
     * @inheritdoc
     */
    public function createUser($form)
    {
        if (!$this->isAllowed('createUser')) {
            throw new AccessApplicationServiceException(SecurityModule::t('user','Access to the users list restricted'));
        }
        $user = new User(['scenario' => 'create']);
        $user->loadForm($form);
        if (!$user->save()) {
            throw new ApplicationServiceException(SecurityModule::t('user','Cannot save user'));
        }
        return $user;
    }

    /**
     * @inheritdoc
     */
    public function updateUser($form)
    {
        if (!$this->isAllowed('updateUser')) {
            throw new AccessApplicationServiceException(SecurityModule::t('user','Access to user update is restricted'));
        }
        $user = User::findOneEx($form->id);
        $user->setScenario('update');
        $user->loadForm($form);
        if (!$user->save()) {
            throw new ApplicationServiceException(SecurityModule::t('user','Cannot update user'));
        }
        return $user;
    }

    public function changePasswordFromUserCard($form)
    {
        $user = User::findOneEx($form->userId);
        $user->password_hash = $form->password;
        $user->setScenario(User::SCENARIO_CHANGE_PASSWORD);
        if (!$user->save()) {
            throw new ApplicationServiceException(SecurityModule::t('user','Cannot change user\'s password') . Json::encode($user->getErrors()));
        }
        return $user;
    }

    /**
     * @inheritdoc
     */
    public function getUserForm($raw)
    {
        $model = User::ensureWeak($raw);
        $userForm = new UserForm();
//        if ($model->isNewRecord) {
//            $userForm->setScenario('create');
//        }
        $userForm->loadAr($model);
        $userForm->aclRole = ArrayHelper::toArray($model->aclRole);
        $userForm->id = $model->id;
        return $userForm;
    }

    /**
     * @inheritdoc
     */
    public function getUserList(UserFinder $form)
    {
        if (!$this->isAllowed('getUserList')) {
            throw new AccessApplicationServiceException(SecurityModule::t('user','Access to the users list is restricted'));
        }
        $query = User::find();
        $query
//            ->distinct(true)
            ->joinWith(['aclRole'])
            ->andFilterWhere([
                'cast("user"."updated_at" as date)' =>
                    empty($form->updatedAt) ? null : \Yii::$app->formatter->asDate($form->updatedAt, CommonHelper::FORMAT_DATE_DB),
                User::tableColumns('status') => $form->status
            ])
            ->andFilterWhere([
                'ilike',
                'login',
                $form->login
            ])
            ->andFilterWhere([
                'ilike',
                AclRole::tableColumns('name'),
                $form->roleName
            ]);
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'login',
                    'statusName' => [
                        'asc' => [
                            'status' => SORT_ASC,
                        ],
                        'desc' => [
                            'status' => SORT_DESC,
                        ],
                    ],
                    'updated_at',
                    'aclRole.name' => [
                        'asc' => [
                            'acl_role.name' => SORT_ASC,
                        ],
                        'desc' => [
                            'acl_role.name' => SORT_DESC,
                        ],
                    ]
                ],
            ],
            'pagination' => [
                'pageSize' => 10
            ],
        ]);
    }

    public function getAccessTokenByLoginAndPassword($login, $password)
    {
        $user = User::find()
            ->where([
                'login' => $login,
            ])
            ->one();
        if ($user && \Yii::$app->getSecurity()->validatePassword($password, $user->password_hash)) {
            $user->access_token = $this->generateAccessToken();
            do {
                $user->access_token = $this->generateAccessToken();
            } while (!$user->save());
            return $user->access_token;
        }
        return null;
    }

    private function generateAccessToken()
    {
        return \Yii::$app->security->generateRandomString(8);
    }

    /**
     * @param IdentityInterface $identity
     * @param $duration
     * @return bool
     */
    public function login(IdentityInterface $identity, $duration)
    {
        return \Yii::$app->user->login($identity, $duration);
    }
}
