<?php
namespace app\common\workflow;

/**
 * Interface WorkflowEntityInterface
 * Domain Layer
 * @package Common\Workflow
 * @copyright 2012-2019 Medkey
 *
 */
interface WorkflowEntityInterface
{
    /**
     * @return mixed
     */
    public function getStatusName();

    /**
     * @return mixed
     */
    public static function statuses();
}
