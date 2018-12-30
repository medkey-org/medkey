<?php
namespace app\modules\crm;

use app\modules\crm\application\OrderService;
use app\modules\crm\application\OrderServiceInterface;
use app\modules\crm\handlers\OrderHandler;
use app\modules\crm\handlers\OrderHandlerInterface;
use yii\base\BootstrapInterface;

/**
 * Class Bootstrap
 * @package Module\CRM
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
            OrderServiceInterface::class => OrderService::class,
            OrderHandlerInterface::class => OrderHandler::class,
        ]);
    }
}
