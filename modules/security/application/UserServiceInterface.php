<?php
namespace app\modules\security\application;

use app\modules\security\models\finders\UserFinder;

/**
 * Interface UserServiceInterface
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
interface UserServiceInterface
{
    public function getCurrentUserLanguage(): ?string;
    public function getUserList(UserFinder $form);
    public function getAccessTokenByLoginAndPassword($login, $password);
    public function getUserForm($raw);
    public function createUser($form);
    public function updateUser($form);
    public function changePasswordFromUserCard($form);
}
