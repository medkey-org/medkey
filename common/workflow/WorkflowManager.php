<?php
namespace app\common\workflow;

use app\common\db\ActiveRecord;
use app\modules\config\application\WorkflowServiceInterface;
use app\modules\config\models\orm\WorkflowStatus;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\MarkingStore\SingleStateMarkingStore;
use yii\base\InvalidValueException;

/**
 * Class WorkflowService
 * @package Common\Workflow
 * @copyright 2012-2019 Medkey
 */
class WorkflowManager implements WorkflowManagerInterface
{
    /**
     * @var WorkflowServiceInterface
     */
    public $workflowService;

    /**
     * WorkflowManager constructor.
     * @param WorkflowServiceInterface $workflowService
     */
    public function __construct(WorkflowServiceInterface $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    /**
     * @param WorkflowParamsDto $dto
     * @return mixed
     * @throws \Exception
     */
    public function applyTransition(WorkflowParamsDto $dto)
    {
        $model = $this->modelFactory($dto->getOrmModule(), $dto->getOrmClass(), $dto->getOrmId());
        /** @var StateMachine $stateMachine */
        $stateMachine = $this->stateMachineFactory($dto->getOrmModule(), $dto->getOrmClass(), $dto->getOrmId());
        if (isset($stateMachine) && !$stateMachine->can($model, $dto->getTransitionName())) {
            throw new \Exception('Workflow not found or transition is restricted');
        }
        $stateMachine->apply($model, $dto->getTransitionName());
        $model->save(); // нужно ли тут это?
        return $model;
    }

    /**
     * @param $ormModule
     * @param $ormClass
     * @param $entityId
     * @return StateMachine|null
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function stateMachineFactory($ormModule, $ormClass, $entityId)
    {
        if (empty($entityId) || empty($ormClass) || empty($ormClass)) {
            return null;
        }
        $workflowOrm = \Yii::$container
            ->get(WorkflowServiceInterface::class)
            ->getCurrentWorkflowByEntity($ormModule, $ormClass, $this->modelFactory($ormModule, $ormClass, $entityId));
        if (!isset($workflowOrm)) {
            return null;
        }
        $definition = $this->definitionFactory($workflowOrm->id);
        if (!isset($definition)) {
            return null;
        };
        $dispatcher = $this->dispatcherFactory($workflowOrm->id, $workflowOrm->name);
        return new StateMachine($definition, new SingleStateMarkingStore(WorkflowStatus::STATE_ATTRIBUTE_DEFAULT), $dispatcher, $workflowOrm->name);
    }

    /**
     * @param $workflowId
     * @param $workflowName
     * @return EventDispatcher
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    private function dispatcherFactory($workflowId, $workflowName)
    {
        $transitions = $this->workflowService->getTransitionsByWorkflow($workflowId, true);
        $dispatcher = new EventDispatcher();
        foreach ($transitions as $transition) {
            if (empty($transition->handler_type) || empty($transition->handler_method)) {
                continue;
            }
            $handlerType = \Yii::$container->get(HandlerManagerInterface::class)->handlerFactory($transition->workflow->orm_module, $transition->handler_type);
            $handler = \Yii::$container->get($handlerType);
            $dispatcher->addListener(
                'workflow.' . $workflowName . '.transition.' . $transition->name, // see symfony workflow guide (events...)
                [$handler, $transition->handler_method]
            );
        }
        return $dispatcher;
    }

    /**
     * @param $workflowId
     * @return null|Definition
     */
    public function definitionFactory($workflowId)
    {
        $places = $this->workflowService->getPlacesByWorkflow($workflowId);
        $transitions = $this->workflowService->getTransitionsByWorkflow($workflowId);
        if (empty($places) || empty($transitions)) {
            return null;
        }
        return new Definition($places, $transitions);
    }

    /**
     * @param $module
     * @param $class
     * @param $id
     * @return mixed
     */
    private function modelFactory($module, $class, $id)
    {
        $module = \Yii::$app->getModule($module);
        $nsOrmModule = $module->getOrmNamespace();
        /** @var ActiveRecord $modelClass */
        $modelClass = $nsOrmModule . '\\' . $class;
        if (!class_exists($modelClass)) {
            throw new InvalidValueException('Class not found.');
        }
        $model = $modelClass::ensure($id);
        return $model;
    }
}
