<?php
namespace app\common\rbac;

use app\common\db\ActiveQuery;
use app\common\helpers\ArrayHelper;
use app\common\helpers\ClassHelper;
use app\common\helpers\CommonHelper;
use app\common\logic\orm\AuthItem;
use app\common\logic\orm\AuthItemChild;
use yii\db\ActiveQueryInterface;
use yii\helpers\Inflector;

/**
 * Class ClassPermissionableTrait
 *
 * @mixin \yii\base\Object
 *
 * @property-read Gate $gate
 *
 * @package Common\RBAC
 * @copyright 2012-2019 Medkey
 * @deprecated
 */
trait ClassPermissionableTrait
{
    /**
     * @var Gate
     */
    protected static $_gate;


    /**
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public static function gate()
    {
        if (!static::$_gate) {
            static::$_gate = \Yii::createObject([
                'class' => Gate::className(),
                'target' => static::className(),
                'module' => static::$module,
            ]);
        }

        return static::$_gate;
    }

    /**
     * @return Gate
     */
    public function getGate()
    {
        return static::gate();
    }

    /**
     * @param string|string[]|null $privilege
     * @param bool                 $allowCaching
     * @return bool
     */
    public function can($privilege = null, $allowCaching = true)
    {
        return static::gate()->can($privilege, $allowCaching);
    }

    /**
     * @param string|string[]      $role
     * @param string|string[]|null $privilege
     * @return AuthItemChild[]
     * @throws \Exception
     */
    public function allow($role, $privilege = null)
    {
        return static::gate()->allow($role, $privilege);
    }

    /**
     * @param string|string[]      $role
     * @param string|string[]|null $privilege
     * @return AuthItemChild[]
     */
    public function deny($role, $privilege = null)
    {
        return static::gate()->deny($role, $privilege);
    }

    /**
     * @param string|string[] $role
     * @return AuthItemChild[]
     */
    public function allowAll($role)
    {
        return static::gate()->allowAll($role);
    }

    /**
     * @param string|string[] $role
     * @return AuthItemChild[]
     */
    public function denyAll($role)
    {
        return static::gate()->denyAll($role);
    }

    /**
     * @return \app\common\logic\orm\AuthItemChild[]
     */
    public function denyAllForAll()
    {
        return static::gate()->denyAllForAll();
    }
}
