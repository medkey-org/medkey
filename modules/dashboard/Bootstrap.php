<?php
namespace app\modules\dashboard;

use app\modules\crm\application\OrderService;
use app\modules\crm\application\OrderServiceInterface;
use app\modules\dashboard\models\repositories\DashboardRepository;
use app\modules\dashboard\models\repositories\DashboardRepositoryInterface;
use app\modules\dashboard\application\DashboardService;
use app\modules\dashboard\application\DashboardServiceInterface;
use yii\base\BootstrapInterface;

/**
 * Class Bootstrap
 * @package Module\Dashboard
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
            DashboardServiceInterface::class => DashboardService::class,
            DashboardRepositoryInterface::class => DashboardRepository::class,
            OrderServiceInterface::class => OrderService::class,
        ]);
    }
}
