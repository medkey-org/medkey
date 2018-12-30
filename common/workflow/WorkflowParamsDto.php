<?php
namespace app\common\workflow;

/**
 * Class WorkflowParamsDto
 * @package Common\Workflow
 * @copyright 2012-2019 Medkey
 */
class WorkflowParamsDto
{
    private $ormModule;
    private $ormClass;
    private $ormId;
    private $transitionName;

    /**
     * WorkflowParamsDto constructor.
     * @param $ormModule
     * @param $ormClass
     * @param $ormId
     * @param $transitionName
     */
    public function __construct($ormModule, $ormClass, $ormId, $transitionName)
    {
        $this->ormModule = $ormModule;
        $this->ormClass = $ormClass;
        $this->ormId = $ormId;
        $this->transitionName = $transitionName;
    }

    /**
     * @return mixed
     */
    public function getOrmModule()
    {
        return $this->ormModule;
    }

    /**
     * @return mixed
     */
    public function getOrmClass()
    {
        return $this->ormClass;
    }

    /**
     * @return mixed
     */
    public function getOrmId()
    {
        return $this->ormId;
    }

    /**
     * @return mixed
     */
    public function getTransitionName()
    {
        return $this->transitionName;
    }
}
