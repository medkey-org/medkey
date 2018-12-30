<?php
namespace app\common\workflow;

/**
 * Class Transition
 * @package Common\Workflow
 * @copyright 2012-2019 Medkey
 *
 */
class Transition extends \Symfony\Component\Workflow\Transition
{
    private $middleWare;

    /**
     * Transition constructor.
     * @param string $name
     * @param $froms
     * @param $tos
     * @param $middleware
     */
    public function __construct(string $name, $froms, $tos, $middleware)
    {
        parent::__construct($name, $froms, $tos);
        $this->middleWare = $middleware;
    }

    /**
     * @return mixed
     */
    public function getMiddleware()
    {
        return $this->middleWare;
    }
}
