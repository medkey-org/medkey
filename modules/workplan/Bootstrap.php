<?php
namespace app\modules\workplan;

use app\modules\medical\application\AttendanceService;
use app\modules\medical\application\AttendanceServiceInterface;
use app\modules\workplan\application\WorkplanService;
use app\modules\workplan\application\WorkplanServiceInterface;
use yii\base\BootstrapInterface;

/**
 * Class Bootstrap
 * @package Module\Workplan
 * @copyright 2012-2019 Medkey
 */
class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        \Yii::$container->setSingletons([
            WorkplanServiceInterface::class => WorkplanService::class,
            AttendanceServiceInterface::class => AttendanceService::class,
        ]);
    }
}
