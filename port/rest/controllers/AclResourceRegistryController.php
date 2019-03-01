<?php
namespace app\port\rest\controllers;

use app\common\acl\resource\ApplicationResourceInterface;
use app\common\filters\QueryParamAuth;
use app\common\helpers\ClassHelper;
use app\common\rest\Controller;
use app\common\service\ServiceRegistry;
use yii\base\Module;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

/**
 * Class AclResourceRegistryController
 * @package Common\REST
 * @copyright 2012-2019 Medkey
 */
class AclResourceRegistryController extends Controller
{
    /**
     * @var ServiceRegistry
     */
    public $registry;

    /**
     * ServiceRegistryController constructor.
     * @param string $id
     * @param Module $module
     * @param ServiceRegistry $registry
     * @param array $config
     */
    public function __construct($id, Module $module, ServiceRegistry $registry, array $config = [])
    {
        $this->registry = $registry;
        parent::__construct($id, $module, $config);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'authenticator' => [
                'class' => QueryParamAuth::class,
                'isSession' => false,
                'optional' => [
                    '*',
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function () {
                    throw new ForbiddenHttpException(\Yii::t('yii', 'You are not allowed to perform this action.'));
                }
            ],
        ]);
    }
    
    public function actionRegistry()
    {
        $module = \Yii::$app->request->getQueryParam('module');
        $aclType = (int)\Yii::$app->request->getQueryParam('aclType');
        return $this->asJson(\Yii::$app->acl->resourceRegistry($module, $aclType));
    }

    public function actionPrivileges()
    {
        $module = \Yii::$app->request->getQueryParam('module');
        $aclType = (int)\Yii::$app->request->getQueryParam('aclType');
        $entityType = \Yii::$app->request->getQueryParam('entityType');
        $service = \Yii::$app->acl->getResourceClass($module, $entityType, $aclType);
        if (!ClassHelper::implementsInterface($service, ApplicationResourceInterface::class)) {
            return [];
        }
        return $this->asJson(\Yii::createObject($service)->getPrivileges());
    }
}
