<?php
namespace app\common\queue;

use yii\queue\JobInterface;

/**
 * Class AsyncServiceJob
 * @package Common\Queue
 * @copyright 2012-2019 Medkey
 */
class AsyncServiceJob implements JobInterface
{
    private $serviceInterface;
    private $method;
    /**
     * [ value1, value2 ... ]
     * @var array
     */
    private $params = [];

    /**
     * AsyncServiceJob constructor.
     * @param $serviceInterface
     * @param $method
     * @param $params
     */
    public function __construct($serviceInterface, $method, $params)
    {
        $this->serviceInterface = $serviceInterface;
        $this->method = $method;
        $this->params = $params;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($queue)
    {
        $service = \Yii::createObject($this->serviceInterface); // DI wrapper
        if (method_exists($service, $this->method)) {
            call_user_func_array([$service, $this->method], $this->params);
        }
    }
}
