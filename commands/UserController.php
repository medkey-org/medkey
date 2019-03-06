<?php
namespace app\commands;

use app\common\console\Controller;
use app\modules\security\application\UserServiceInterface;

/**
 * Class UserController
 * @package Common\CLI
 * @copyright 2012-2019 Medkey
 */
class UserController extends Controller
{
    private $userService;

    /**
     * Noop
     */
    public function actionChangePassword()
    {
    }
}
