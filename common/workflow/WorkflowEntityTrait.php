<?php
namespace app\common\workflow;

use app\common\helpers\ClassHelper;
use app\modules\config\application\WorkflowStatusServiceInterface;

/**
 * Trait WorkflowEntityTrait
 * @package Common\Workflow
 * @copyright 2012-2019 Medkey
 *
 */
trait WorkflowEntityTrait
{
    /**
     * @return string
     */
    public function getStatusName()
    {
        $statuses = static::statuses();
        return isset($statuses[$this->status]) ? $statuses[$this->status] : null;
    }

    /**
     * Need to be refactored because of layers intersection
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function getStartStatus()
    {
        /** @var WorkflowStatusServiceInterface $service */
        $service = \Yii::$container->get(WorkflowStatusServiceInterface::class);
        return $service
            ->getStartStatusByEntity(
                ClassHelper::getMatchModule(static::class, false),
                ClassHelper::getShortName(static::class)
            );
    }

    /**
     * Need to be refactored because of layers intersection
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public static function statuses()
    {
        /** @var WorkflowStatusServiceInterface $service */
        $service = \Yii::$container->get(WorkflowStatusServiceInterface::class);
        return $service
            ->getWorkflowStatusesByEntity(
                ClassHelper::getMatchModule(static::class, false),
                ClassHelper::getShortName(static::class)
            );
    }
}
