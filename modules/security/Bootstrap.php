<?php
namespace app\modules\security;

use app\common\acl\resource\ResourceInterface;
use app\common\acl\role\RoleInterface;
use app\common\acl\RuleAssertion;
use app\modules\security\application\AclService;
use app\modules\security\models\orm\Acl;
use app\modules\security\models\orm\AclRole;
use app\modules\security\application\AclServiceInterface;
use app\modules\security\application\UserService;
use app\modules\security\application\UserServiceInterface;
use app\modules\security\domain\services\ResponsibilityService;
use app\modules\security\domain\services\ResponsibilityServiceInterface;
use yii\base\BootstrapInterface;

/**
 * Class Bootstrap
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        \Yii::$container->setSingletons([
            UserServiceInterface::class => UserService::class,
            AclServiceInterface::class => AclService::class,
            ResponsibilityServiceInterface::class => ResponsibilityService::class,
        ]);
        $this->populateAcl();
    }

    public function populateAcl()
    {
        /** @var \app\common\acl\Acl $acl */
        $acl = \Yii::$app->acl;
        if (!\Yii::$app->db->schema->getTableSchema('{{%acl}}')) {
            return null;
        }
        $aclOrm = Acl::find()
            ->notDeleted()
            ->all();
        $aclRoleOrm = AclRole::find()
            ->notDeleted()
            ->all();
        foreach ($aclRoleOrm as $r) { // todo parent считывать
            if (!$r instanceof RoleInterface) {
                throw new \Exception('Ошибка сервера (app init). Обратитесь к администратору.');
            }
            if (!$acl->hasRole($r)) {
                $acl->addRole($r);
            }
        }
        foreach ($aclOrm as $a) {
            $resourceClass = \Yii::$app->acl->getResourceClass($a->module, $a->entity_type, $a->type);
            if (is_null($resourceClass)) {
                \Yii::debug('Application::populateAcl(): not found module "' . $a->module . '", entity_type "' . $a->entity_type . '", type "' . $a->type . '"');
                continue;
            }
            $resource = \Yii::createObject($resourceClass);
            // todo register singleton по interface (нужен небольшой рефакторинг
            // todo по переносу в БД интерфейсов
            $role = $a->aclRole;
            $action = $a->action;
            if (
                null === $role
                || empty($action)
                || !$resource instanceof ResourceInterface
                || !$role instanceof RoleInterface
            ) {
                throw new \Exception('Ошибка сервера (app init). Обратитесь к администратору.');
            }
            if (!$acl->hasResource($resource)) {
                $acl->addResource($resource);
            }
//            if ($typeAcl === Acl::TYPE_ACL_ALLOW) {
            $acl->allow($role, $resource, $a->action, new RuleAssertion($a->rule));
//            } elseif ($typeAcl === Acl::TYPE_ACL_DENY) {
//                $acl->deny($role, $resource, $a->action, new RuleAssertion($a->rule));
//            }
        }
    }
}
