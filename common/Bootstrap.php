<?php
namespace app\common;

use app\common\bots\SkypeBotService;
use app\common\bots\SkypeBotServiceInterface;
use app\common\mail\EmailSenderService;
use app\common\mail\EmailSenderServiceInterface;
use app\common\notification\NotificationService;
use app\common\notification\NotificationServiceInterface;
use app\common\service\ServiceRegistry;
use app\common\workflow\HandlerManager;
use app\common\workflow\HandlerManagerInterface;
use app\common\workflow\WorkflowManager;
use app\common\workflow\WorkflowManagerInterface;
use yii\base\BootstrapInterface;

/**
 * Class Bootstrap
 * @package Common
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
            WorkflowManagerInterface::class => WorkflowManager::class,
            NotificationServiceInterface::class => NotificationService::class,
            HandlerManagerInterface::class => HandlerManager::class,
            SkypeBotServiceInterface::class => SkypeBotService::class,
            EmailSenderServiceInterface::class => EmailSenderService::class,
            'serviceRegistry' => ServiceRegistry::class,
        ]);
    }
}
