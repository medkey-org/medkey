<?php
namespace app\common\rbac;

use app\common\base\InitTrait;
use app\common\db\ActiveRecord;
use app\common\logic\orm\AuthItemChild;

/**
 * Class PermissionableTrait
 *
 * @mixin ActiveRecord
 * @mixin ClassPermissionableTrait
 * @mixin InitTrait
 *
 * @property-read Gate $gate
 *
 * @package Common\RBAC
 * @copyright 2012-2019 Medkey
 *
 * @deprecated
 */
trait PermissionableTrait
{
    /**
     * @var Gate
     */
    protected $_modelGate;


    /**
     * @see InitTrait
     */
    public function initPermissionableTrait()
    {
        if (!$this instanceof ActiveRecord) {
            throw new \Exception('Invalid trait use: class must be ActiveRecord');
        }

        // инициализируем гейт
        $this->createGate();
    }

    /**
     * @param bool $classTarget
     * @return Gate
     */
    public function getGate($classTarget = false)
    {
        if ($classTarget) {
            return static::gate();
        }
        if (!$this->_modelGate) {
            $this->createGate();
        }

        return $this->_modelGate;
    }

    /**
     * @return Gate
     */
    protected function createGate()
    {
        return $this->_modelGate = \Yii::createObject([
            'class' => Gate::className(),
            'target' => $this,
            'module' => static::$module,
        ]);
    }

    /**
     * @param string|string[]|null $privilege
     * @param bool                 $allowCaching
     * @return bool
     */
    public function can($privilege = null, $allowCaching = true)
    {
        return $this->getGate(true)->can($privilege, $allowCaching) || $this->getGate(false)->can($privilege, $allowCaching);
    }

    /**
     * @param string|string[]      $role
     * @param string|string[]|null $privilege
     * @param bool                 $classTarget
     * @return AuthItemChild[]
     * @throws \Exception
     */
    public function allow($role, $privilege = null, $classTarget = false)
    {
        return $this->getGate($classTarget)->allow($role, $privilege);
    }

    /**
     * @param string|string[]      $role
     * @param string|string[]|null $privilege
     * @param bool                 $classTarget
     * @return AuthItemChild[]
     */
    public function deny($role, $privilege = null, $classTarget = false)
    {
        return $this->getGate($classTarget)->deny($role, $privilege);
    }

    /**
     * @param string|string[] $role
     * @param bool            $classTarget
     * @return AuthItemChild[]
     */
    public function allowAll($role, $classTarget = false)
    {
        return $this->getGate($classTarget)->allowAll($role);
    }

    /**
     * @param string|string[] $role
     * @param bool            $classTarget
     * @return AuthItemChild[]
     */
    public function denyAll($role, $classTarget = false)
    {
        return $this->getGate($classTarget)->denyAll($role);
    }

    /**
     * @param bool $classTarget
     * @return \app\common\logic\orm\AuthItemChild[]
     */
    public function denyAllForAll($classTarget = false)
    {
        return $this->getGate($classTarget)->denyAllForAll();
    }

    /**
     * @param string|string[]      $role
     * @param string|string[]|null $privilege
     * @return AuthItemChild[]
     */
    public function denyBothGate($role, $privilege = null)
    {
        return array_merge($this->deny($role, $privilege, true), $this->deny($role, $privilege, false));
    }

    /**
     * @param string|string[] $role
     * @return AuthItemChild[]
     */
    public function denyAllBothGate($role)
    {
        return array_merge($this->denyAll($role, true), $this->denyAll($role, false));
    }

    /**
     * @return AuthItemChild[]
     */
    public function denyAllForAllBothGate()
    {
        return array_merge($this->denyAllForAll(true), $this->denyAllForAll(false));
    }
}
