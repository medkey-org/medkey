<?php
namespace app\common\workflow;

/**
 * Interface HandlerRegistryInterface
 * @package Common\Workflow
 * @copyright 2012-2019 Medkey
 */
interface HandlerManagerInterface
{
    public function registry($module, $withNs = false);
    public function handlerFactory($module, $handlerType);
}
