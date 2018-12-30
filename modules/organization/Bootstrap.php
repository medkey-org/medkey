<?php
namespace app\modules\organization;

use app\modules\organization\application\EmployeeService;
use app\modules\organization\application\EmployeeServiceInterface;
use yii\base\BootstrapInterface;

/**
 * Class Bootstrap
 * @package Module\Organization
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
            EmployeeServiceInterface::class => EmployeeService::class,
        ]);
    }
}
