<?php
namespace app\modules\location;

use app\modules\location\application\LocationService;
use app\modules\location\application\LocationServiceInterface;
use yii\base\BootstrapInterface;

/**
 * Class Bootstrap
 * @package Module\Location
 * @copyright 2012-2019 Medkey
 */
class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        return \Yii::$container->setSingletons([
            LocationServiceInterface::class => LocationService::class,
        ]);
    }
}
