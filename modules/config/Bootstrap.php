<?php
namespace app\modules\config;

use app\modules\config\application\ConfigService;
use app\modules\config\application\ConfigServiceInterface;
use app\modules\config\application\DirectoryService;
use app\modules\config\application\DirectoryServiceInterface;
use app\modules\config\application\WorkflowServiceInterface;
use app\modules\config\application\WorkflowService;
use app\modules\config\application\WorkflowStatusServiceInterface;
use app\modules\config\application\WorkflowStatusService;
use app\modules\config\application\WorkflowTransitionServiceInterface;
use app\modules\config\application\WorkflowTransitionService;
use yii\base\BootstrapInterface;

/**
 * Class Bootstrap
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * {@inheritdoc}
     */
    public function bootstrap($app)
    {
        \Yii::$container->setSingletons([
            DirectoryServiceInterface::class => DirectoryService::class,
            WorkflowServiceInterface::class => WorkflowService::class,
            WorkflowStatusServiceInterface::class => WorkflowStatusService::class,
            WorkflowTransitionServiceInterface::class => WorkflowTransitionService::class,
            ConfigServiceInterface::class => ConfigService::class,
        ]);
    }
}
