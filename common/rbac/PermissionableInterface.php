<?php
namespace app\common\rbac;

/**
 * Interface PermissionableInterface
 *
 * @package Common\RBAC
 * @copyright 2012-2019 Medkey
 *
 * @deprecated
 */
interface PermissionableInterface
{
    /**
     * @param string|string[]|null $privilege
     * @param bool                 $allowCaching
     * @return bool
     */
    public function can($privilege = null, $allowCaching = true);

    /**
     * @param string|string[]      $role
     * @param string|string[]|null $privilege
     */
    public function allow($role, $privilege = null);

    /**
     * @param string|string[]      $role
     * @param string|string[]|null $privilege
     */
    public function deny($role, $privilege = null);
}
