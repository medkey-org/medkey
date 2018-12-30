<?php
namespace app\common\rbac;

use app\common\db\ActiveRecord;
use app\common\helpers\ClassHelper;
use app\common\db\ChangeAttributeEvent;
use app\common\helpers\ArrayHelper;
use app\common\helpers\CommonHelper;
use app\common\logic\orm\AuthItem;
use app\common\logic\orm\AuthItemChild;
use yii\base\Event;
use yii\db\AfterSaveEvent;
use yii\helpers\Inflector;
use yii\base\BaseObject;

/**
 * Class Gate
 *
 * @property-read string|ActiveRecord $targetClassName
 * @property-read string              $targetShortName
 * @property-read string              $targetClassId
 * @property-read string              $targetId
 * @property-read string              $targetTitle
 *
 * @property-read int    $modelId
 * @property-read string $modelTitle
 *
 * @property-read array           $privilegeParams
 * @property-read string          $privilegeLabel
 * @property-read string          $permissionName
 * @property-read string          $permissionTitle
 * @property-read AuthItem|null   $permission
 * @property-read AuthItem[]      $allPermissions
 * @property-read AuthItemChild   $authItemChild
 * @property-read AuthItemChild[] $allAuthItemChilds
 * @property-read array           $defaultModelPrivilegesLabels
 * @property-read array           $defaultClassPrivilegesLabels
 * @property-read array           $defaultModelPrivileges
 * @property-read array           $defaultClassPrivileges
 *
 * @package Common\RBAC
 * @copyright 2012-2019 Medkey
 *
 * @deprecated
 */
class Gate extends BaseObject
{
    /**
     * Model class name or model instance
     *
     * @var string|ActiveRecord
     */
    public $target;
    /**
     * Module name
     *
     * @var string
     */
    public $module;
    /**
     * Privilege list
     *
     * @var array
     */
    public $privileges;
    /**
     * Labels list for building permission name
     * @see getDefaultClassPrivilegesLabels()
     * @see getDefaultModelPrivilegesLabels()
     *
     * @var array
     */
    public $privilegesLabels = [];
    /**
     * List of permission attributes
     * @see can()
     *
     * @var array
     */
    public $privilegesParams = [];


    /**
     * @throws \Exception
     */
    public function init()
    {
        if (!$this->privileges) {
            $this->privileges = $this->isModelMode() ? $this->defaultModelPrivileges : $this->defaultClassPrivileges;
        }
        $this->privilegesLabels = array_merge(
            $this->isModelMode() ? $this->defaultModelPrivilegesLabels : $this->defaultClassPrivilegesLabels,
            $this->privilegesLabels
        );

        if ($this->isModelMode()) {
            if (!$this->target instanceof ActiveRecord) {
                throw new \Exception('Invalid target given: must be class name or ActiveRecord object');
            }
            $className = $this->targetClassName;
            $title = $className::getTitleAttribute();

            $this->target->on(ActiveRecord::EVENT_AFTER_INSERT, [$this, 'createModelPermissions']);
            $this->target->on(ActiveRecord::EVENT_AFTER_DELETE, [$this, 'deleteModelPermissions']);
            $this->target->on($className::eventAfterChangeAttribute($title), [$this, 'changeModelPermissionsTitle']);
        }
    }

    /**
     * @param string|string[]|null $privilege
     * @param bool                 $allowCaching
     * @return bool
     */
    public function can($privilege = null, $allowCaching = true)
    {
        if (is_array($privilege)) {
            return array_reduce($privilege, function ($result, $privilege) use ($allowCaching) {
                return $result && $this->can($privilege, $allowCaching);
            }, true);
        }
        $params = CommonHelper::optionsValue($this->getPrivilegeParams($privilege), $this);

        return \Yii::$app->user->can($this->getPermissionName($privilege), $params, $allowCaching);
    }

    /**
     * @param string|string[]      $role
     * @param string|string[]|null $privilege
     * @return AuthItemChild[]
     * @throws \Exception
     */
    public function allow($role, $privilege = null)
    {
        $roles = (array) $role;
        $privileges = (array) $privilege;
        $authItemChilds = $this->getAuthItemChild($roles, $privileges);

        foreach ($roles as $role) {
            foreach ($privileges as $privilege) {
                /** @var AuthItemChild $item */
                $item = ArrayHelper::findBy($authItemChilds, [
                    'parent' => $role,
                    'child' => $this->getPermissionName($privilege),
                ]);

                if (!$item) {
                    $item = $this->createAuthItemChild($role, $privilege);

                    if (!$item->validate()) {
                        throw new \Exception('Invalid auth item model: ' . implode(', ', ArrayHelper::flatten($item->errors)));
                    }
                    $item->save();
                }
                $authItemChilds[] = $item;
            }
        }

        return $authItemChilds;
    }

    /**
     * @param string|string[]      $role
     * @param string|string[]|null $privilege
     * @return AuthItemChild[]
     */
    public function deny($role, $privilege = null)
    {
        $roles = $role ? $role : ((array) $role);
        $privileges = is_array($privilege) ? $privilege : [$privilege];
        $authItemChilds = $this->getAuthItemChild($roles, $privileges);

        foreach ($authItemChilds as $item) {
            $item->delete();
        }

        return $authItemChilds;
    }

    /**
     * @internal
     * @param AfterSaveEvent $e
     * @throws \Exception
     */
    public function createModelPermissions(AfterSaveEvent $e)
    {
        $this->createAllPermissions();
    }

    /**
     * @internal
     * @param Event $e
     */
    public function deleteModelPermissions(Event $e)
    {
        /** @var AuthItem $permission */
        foreach ($this->getAllPermissions() as $permission) {
            if (!$permission) {
                continue;
            }
            $permission->delete();
        }
        /** @var AuthItemChild $item */
        foreach ($this->getAllAuthItemChilds() as $item) {
            if (!$item) {
                continue;
            }
            $item->delete();
        }
    }

    /**
     * @internal
     * @param ChangeAttributeEvent $e
     */
    public function changeModelPermissionsTitle(ChangeAttributeEvent $e)
    {
        if ($this->target->isNewRecord) {
            return;
        }
        $permissions = $this->getAllPermissions();

        foreach ($this->privileges as $privilege) {
            $permission = ArrayHelper::findBy($permissions, [
                'name' => $this->getPermissionName($privilege),
            ]);

            if (!$permission) {
                $permission = $this->createPermission($privilege);
            }
            $permission->title = $this->getPermissionTitle($privilege);
            $permission->save();
        }
    }

    /**
     * @param string|string[] $role
     * @return AuthItemChild[]
     */
    public function allowAll($role)
    {
        return $this->allow($role, $this->privileges);
    }

    /**
     * @param string|string[] $role
     * @return AuthItemChild[]
     */
    public function denyAll($role)
    {
        return $this->deny($role, $this->privileges);
    }

    /**
     * @return \app\common\logic\orm\AuthItemChild[]
     */
    public function denyAllForAll()
    {
        return $this->denyAll(null);
    }

    /**
     * @param string|string[]|null $role
     * @param string|string[]|null $privilege
     * @return AuthItemChild|AuthItemChild[]
     */
    public function getAuthItemChild($role = null, $privilege = null)
    {
        $query = AuthItemChild::find()
            ->notDeleted()
            ->currentUnion()
            ->andFilterWhere([
                'parent' => $role,
            ])
            ->andWhere([
                'child' => $this->getPermissionName($privilege),
            ]);

        return (is_array($role) || is_array($privilege)) ? $query->all() : $query->one();
    }

    /**
     * @param string|string[]|null $role
     * @return AuthItemChild[]
     */
    public function getAllAuthItemChilds($role = null)
    {
        return $this->getAuthItemChild($role, $this->privileges);
    }

    /**
     * @param string      $role
     * @param string|null $privilege
     * @return AuthItemChild
     */
    public function createAuthItemChild($role, $privilege = null)
    {
        $item = new AuthItemChild();
        $item->setAttributes([
            'parent' => $role,
            'child' => $this->getPermissionName($privilege),
        ]);

        return $item;
    }

    /**
     * @throws \Exception
     */
    public function createAllPermissions()
    {
        $permissions = $this->getAllPermissions();

        foreach ($this->privileges as $privilege) {
            $item = ArrayHelper::findBy($permissions, [
                'name' => $this->getPermissionName($privilege),
            ]);

            if ($item) {
                continue;
            }
            $item = $this->createPermission($privilege);

            if (!$item->validate()) {
                throw new \Exception('Invalid auth item model: ' . implode(', ', ArrayHelper::flatten($item->errors)));
            }
            $item->save(false);
        }
    }

    /**
     * @param string|null $privilege
     * @return AuthItem
     */
    public function createPermission($privilege = null)
    {
        $item = new AuthItem();
        $item->setAttributes([
            'type' => AuthItem::TYPE_PERMISSION,
            'name' => $this->getPermissionName($privilege),
            'title' => $this->getPermissionTitle($privilege),
            'hidden' => true,
        ]);

        return $item;
    }

    /**
     * @param string|null $privilege
     * @return AuthItem|AuthItem[]|null
     */
    public function getPermission($privilege = null)
    {
        $query = AuthItem::find()
            ->currentUnion()
            ->notDeleted()
            ->andWhere([
                'name' => $this->getPermissionName($privilege),
            ]);

        return is_array($privilege) ? $query->all() : $query->one();
    }

    /**
     * @return AuthItem|AuthItem[]|null
     */
    public function getAllPermissions()
    {
        return $this->getPermission($this->privileges);
    }

    /**
     * @param string|null $privilege
     * @return string|null
     */
    public function getPrivilegeLabel($privilege = null)
    {
        if (!$privilege) {
            $privilege = '_empty';
        }

        return ArrayHelper::getValue($this->privilegesLabels, $privilege);
    }

    /**
     * @param string|null $privilege
     * @return array
     */
    public function getPrivilegeParams($privilege = null)
    {
        if (!$privilege) {
            $privilege = '_empty';
        }

        return ArrayHelper::getValue($this->privilegesParams, $privilege, []);
    }

    /**
     * @param string|string[]|null $privilege
     * @return string|string[]
     */
    public function getPermissionName($privilege = null)
    {
        if (is_array($privilege)) {
            return array_map(function ($privilege) {
                return $this->getPermissionName($privilege);
            }, $privilege);
        }
        $module = $this->module;
        $modelId = $this->targetId;

        return "{$module}_{$modelId}" . ($privilege ? "_{$privilege}" : '');
    }

    /**
     * @param string|string[]|null $privilege
     * @return string
     */
    public function getPermissionTitle($privilege = null)
    {
        if (is_array($privilege)) {
            return array_map(function ($privilege) {
                return $this->getPermissionTitle($privilege);
            }, $privilege);
        }
        $privilegeLabel = $this->getPrivilegeLabel($privilege);
        $modelTitle = $this->targetTitle;

        return trim("{$privilegeLabel} {$modelTitle}");
    }

    /**
     * @return string|ActiveRecord class name
     */
    public function getTargetClassName()
    {
        return $this->isModelMode() ? get_class($this->target) : $this->target;
    }

    /**
     * @return string
     */
    public function getTargetShortName()
    {
        return ClassHelper::getShortName($this->targetClassName);
    }

    /**
     * @return string
     */
    public function getTargetClassId()
    {
        return Inflector::camel2id($this->targetShortName);
    }

    /**
     * @return int|null
     */
    public function getModelId()
    {
        if (!$this->isModelMode()) {
            return null;
        }

        return $this->target->id;
    }

    /**
     * @return string|null
     */
    public function getModelTitle()
    {
        if (!$this->isModelMode()) {
            return null;
        }

        return $this->target->titleValue;
    }

    /**
     * @return string
     */
    public function getTargetId()
    {
        $model = $this->targetClassId;
        $id = $this->modelId;

        return $this->isModelMode() ? "{$model}-{$id}" : $model;
    }

    /**
     * @return string
     */
    public function getTargetTitle()
    {
        $modelClass = $this->targetClassName;
        $modelTitle = method_exists($modelClass, 'modelTitle') ? $modelClass::modelTitle() : $this->targetShortName;
        $title = $this->modelTitle;

        return trim("{$modelTitle} {$title}");
    }

    /**
     * @return bool
     */
    public function isModelMode()
    {
        return is_object($this->target);
    }

    /**
     * @return array
     */
    public function getDefaultClassPrivilegesLabels()
    {
        return [
            'view'   => 'Просмотр объектов',
            'create' => 'Создание объектов',
            'edit'   => 'Редактирование объектов',
            'del'    => 'Удаление объектов',

            '_empty' => 'Управление объектами', // если пермишен без привилегии
        ];
    }

    /**
     * @return array
     */
    public function getDefaultModelPrivilegesLabels()
    {
        return [
            'view'   => 'Просмотр объекта',
            'create' => 'Создание объекта',
            'edit'   => 'Редактирование объекта',
            'del'    => 'Удаление объекта',

            '_empty' => 'Управление объектом', // если пермишен без привилегии
        ];
    }

    /**
     * @return array
     */
    public function getDefaultClassPrivileges()
    {
        return ['view', 'create', 'edit', 'del'];
    }

    /**
     * @return array
     */
    public function getDefaultModelPrivileges()
    {
        return ['view', 'edit', 'del'];
    }
}
