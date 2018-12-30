<?php
namespace app\common\acl;

use app\common\acl\resource\ApplicationResourceInterface;
use app\common\db\ActiveRecord;
use Zend\Permissions\Acl\Acl;
use app\modules\security\models\orm\Acl as AclORM;
use Zend\Permissions\Acl\Assertion\AssertionInterface;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Class RuleAssertion
 * @package Common\ACL
 * @copyright 2012-2019 Medkey
 */
class RuleAssertion implements AssertionInterface
{
    /**
     * Current rules from setting
     * @var array
     */
    private $rule = [];

    /**
     * RuleAssertion constructor.
     * @param $rule
     */
    public function __construct($rule = null)
    {
        $this->rule = $rule;
    }

    /**
     * @inheritdoc
     */
    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null)
    {
        if (\Yii::$app->user->getIsGuest()) {
            return false;
        }

        if (!$resource instanceof ApplicationResourceInterface) {
            return true; // и не нужно проверять ничего
        }
        $proprietary = $resource->getProprietary();
        if (empty($this->rule)) {
            return true;
        }

        if (!$proprietary instanceof ActiveRecord) { // вероятно не нужна проверка, т.к. не передали проверочную модель
            return true;
        }
        // check author
        $check = false;

        if (in_array(AclORM::RULE_AUTHOR, $this->rule)) {
            $check = $proprietary->user_created_id === \Yii::$app->user->getId();
        }
        if (in_array(AclORM::RULE_RESPONSIBILITY, $this->rule)) {
            // todo
        }
        if (in_array(AclORM::RULE_DEPARTMENT, $this->rule)) {
            // todo
        }
        if (in_array(AclORM::RULE_POSITION, $this->rule)) {
            // todo
        }
        if (in_array(AclORM::RULE_USER_ORGANIZATION, $this->rule)) {
            $check = ($proprietary->organization_id === \Yii::$app->user->getIdentity()->organization_id);
        }
        return $check;
    }
}
