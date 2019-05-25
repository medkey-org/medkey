<?php
namespace app\modules\medical;

use app\modules\medical\application\AttendanceService;
use app\modules\medical\application\AttendanceServiceInterface;
use app\modules\medical\application\EhrService;
use app\modules\medical\application\EhrServiceInterface;
use app\modules\medical\application\PatientService;
use app\modules\medical\application\PatientServiceInterface;
use app\modules\medical\application\PolicyService;
use app\modules\medical\application\PolicyServiceInterface;
use app\modules\medical\application\ReferralService;
use app\modules\medical\application\ReferralServiceInterface;
use app\modules\medical\application\ScheduleService;
use app\modules\medical\application\ScheduleServiceInterface;
use app\modules\medical\application\ServicePriceService;
use app\modules\medical\application\ServicePriceServiceInterface;
use yii\base\BootstrapInterface;

/**
 * Class Bootstrap
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * {@inheritDoc}
     */
    public function bootstrap($app)
    {
        \Yii::$container->setSingletons([
            ReferralServiceInterface::class => ReferralService::class,
            EhrServiceInterface::class => EhrService::class,
            AttendanceServiceInterface::class => AttendanceService::class,
            PatientServiceInterface::class => PatientService::class,
            PolicyServiceInterface::class => PolicyService::class,
            ServicePriceServiceInterface::class => ServicePriceService::class,
            ScheduleServiceInterface::class => ScheduleService::class,
        ]);
    }
}
