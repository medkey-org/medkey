<?php
namespace app\port\cli\controllers;

use app\common\console\Controller;
use app\modules\security\application\UserServiceInterface;
use app\modules\security\models\form\User;

/**
 * Class UserController
 * @package Common\CLI
 * @copyright 2012-2019 Medkey
 */
class UserController extends Controller
{
    private $userService;

    /**
     * UserController constructor.
     * @param UserServiceInterface $userService
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Noop
     */
    public function actionChangePassword()
    {
    }
}
