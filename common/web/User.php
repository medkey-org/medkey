<?php
namespace app\common\web;

use app\modules\security\application\UserServiceInterface;
use app\modules\security\models\orm\User as UserOrm;

/**
 * Class User
 * @package Common\Web
 * @copyright 2012-2019 Medkey
 */
class User extends \yii\web\User
{
    public $loginUrl = ['/security/ui/user/login-form'];
    public $userService;

    /**
     * User constructor.
     * @param UserServiceInterface $userService
     * @param array $config
     */
    public function __construct(UserServiceInterface $userService, array $config = [])
    {
        $this->userService = $userService;
        parent::__construct($config);
    }

    /**
     * @return bool
     */
    public function isSuper()
    {
        $user = $this->identity;
        if (is_null($user) || !$user instanceof UserOrm) {
            return false;
        }
        $role = $user->aclRole->name;
        if ($role === getenv('SUPER_LOGIN')) {
            return true;
        }
        return false;
    }

    public function getCurrentLang(): ?string
    {
        return $this->userService->getCurrentUserLanguage();
    }
}
