<?php
namespace app\common\acl;

use app\common\acl\resource\ResourceInterface;
use app\common\helpers\ClassHelper;
use app\common\service\exception\AccessApplicationServiceException;
use app\modules\security\models\orm\User;

/**
 * Trait ApplicationResourceTrait
 * @package Common\ACL
 * @copyright 2012-2019 Medkey
 */
trait ApplicationResourceTrait
{
    private $_proprietary;

    /**
     * @inheritdoc
     */
    public function setProprietary($obj)
    {
        $this->_proprietary = $obj;
    }

    /**
     * @inhe
     */
    public function getProprietary()
    {
        return $this->_proprietary;
    }

    /**
     * @param string $privilege
     * @param string $proprietary
     * @return bool
     * @throws AccessApplicationServiceException
     * @throws \app\common\db\Exception
     */
    public function isAllowed($privilege, $proprietary = null)
    {
        if (\Yii::$app->user->isGuest) {
            throw new AccessApplicationServiceException('Current session must be authenticated.');
        }
        $user = User::findOneEx(\Yii::$app->user->id);
        $role = $user->aclRole;
        if (null === $role) {
            throw new AccessApplicationServiceException('Not found user\'s role.');
        }
        if (!$this instanceof ResourceInterface) {
            throw new AccessApplicationServiceException('Current object is not instance of ACL.');
        }
        /** @var Acl $acl */
        $acl = \Yii::$app->acl;
        if (!$acl->hasResource($this) || !$acl->hasRole($role)) {
            return false;
        }
        if (isset($proprietary)) {
            $this->_proprietary = $proprietary;
        }
        $check = $acl->isAllowed($role, $this, (string)$privilege);
        $this->_proprietary = null;
        return $check;
    }

    /**
     * @inheritdoc
     */
    public function getResourceId()
    {
        return static::class; // with NS = 99.999...% unique on project
    }

    /**
     * @inheritdoc
     */
    public function aclAlias()
    {
        return ClassHelper::getShortName(static::class);
    }

    /**
     * @inheritdoc
     */
    public function getPrivileges()
    {
        return [];
    }
}
