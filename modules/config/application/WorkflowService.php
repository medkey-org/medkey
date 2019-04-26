<?php
namespace app\modules\config\application;

use app\common\data\ActiveDataProvider;
use app\common\service\ApplicationService;
use app\common\service\exception\AccessApplicationServiceException;
use app\common\service\exception\ApplicationServiceException;
use app\modules\config\ConfigModule;
use app\modules\config\models\finders\WorkflowFinder;
use app\modules\config\models\orm\Workflow;
use app\modules\config\models\form\Workflow as WorkflowForm;
use app\modules\config\models\orm\WorkflowEntity;
use app\modules\config\models\orm\WorkflowStatus;
use app\modules\config\models\orm\WorkflowTransition;
use app\common\workflow\Transition;

/**
 * Class WorkflowService
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class WorkflowService extends ApplicationService implements WorkflowServiceInterface
{
    /**
     * @param $id
     * @return mixed
     */
    public function getWorkflowById($id)
    {
        return Workflow::findOneEx($id);
    }

    /**
     * @todo добавить проверку на entity_id/workflow_id, что есть или нет привязки к воркфлоу
     * @inheritdoc
     */
    public function getCurrentWorkflowByEntity($ormModule, $ormClass, $entity)
    {
        // не было воркфлоу на момент создания сущности
        if (!isset($entity->{WorkflowStatus::STATE_ATTRIBUTE_DEFAULT})) {
            return null;
        }
        $workflowEntity = WorkflowEntity::find()
            ->joinWith(['workflow'], true, 'INNER JOIN')
            ->where([
                Workflow::tableColumns('orm_class') => $ormClass,
                Workflow::tableColumns('orm_module') => $ormModule,
                WorkflowEntity::tableColumns('entity_id') => $entity->id,
            ])
            ->notDeleted()
            ->one();
        if (isset($workflowEntity)) {
            return $workflowEntity->workflow;
        }
        $workflow = Workflow::find()
            ->where([
                'orm_module' => $ormModule,
                'orm_class' => $ormClass,
                'status' => Workflow::STATUS_ACTIVE,
            ])
            ->one();
        if (!isset($workflow)) {
            return null;
        }
        $workflowEntity = new WorkflowEntity();
        $workflowEntity->workflow_id = $workflow->id;
        $workflowEntity->entity_id = $entity->id;
        if (!$workflowEntity->save()) {
            throw new ApplicationServiceException('Не удалось связать сущность с workflow.');
        }
        return $workflow;
    }

    /**
     * @inheritdoc
     */
    public function getWorkflowForm($raw)
    {
        $workflow = Workflow::ensureWeak($raw);
        $workflowForm = new WorkflowForm();
        $workflowForm->loadAr($workflow);
        $workflowForm->id = $workflow->id;
        return $workflowForm;
    }

    /**
     * @inheritdoc
     */
    public function createWorkflow($form)
    {
        $workflow = new Workflow([
            'scenario' => 'create',
        ]);
        $workflow->loadForm($form);
        if (!$workflow->save()) {
            throw new ApplicationServiceException('Не удалось создать workflow.');
        }
        return $workflow;
    }

    /**
     * @inheritdoc
     */
    public function updateWorkflow(WorkflowForm $form)
    {
        $workflow = Workflow::findOneEx($form->id);
        $workflow->setScenario('update');
        $workflow->loadForm($form);
        if (!$workflow->save()) {
            throw new ApplicationServiceException('Не удалось обновить workflow.');
        }
        return $workflow;
    }

    /**
     * @inheritdoc
     */
    public function getWorkflowList(WorkflowFinder $form)
    {
        if (!$this->isAllowed('getWorkflowList')) {
            throw new AccessApplicationServiceException('Доступ к списку workflow недоступен.');
        }
        $query = Workflow::find();
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getTransitionsByWorkflow($workflowId, $isOrm = false)
    {
        $workflow = Workflow::findOneEx($workflowId);
        $transitions = $workflow->workflowTransitions;
        $t = [];
        foreach ($transitions as $transition) {
            if (!$transition instanceof WorkflowTransition) {
                throw new ApplicationServiceException('Некорректный статусный переход.');
            }
            if (!$isOrm) {
                $t[] = new Transition(
                    $transition->name,
                    $transition->statusFrom->state_value,
                    $transition->statusTo->state_value,
                    $transition->middleware
                    );
            } else {
                $t[] = $transition;
            }
        }
        return $t;
    }

    /**
     * @todo ловить ошибки, если не заполнены переходы по какой-либо причине
     * @inheritdoc
     */
    public function getPlacesByWorkflow($workflowId)
    {
        $places = [];
        $workflow = Workflow::findOneEx($workflowId);
        $startTransition = $workflow->workflowStartTransition;
        $otherTransitions = $workflow->workflowTransitionsWithoutStart;
        if (
            !$startTransition ||
            !$startTransition->statusFrom instanceof WorkflowStatus ||
            !$startTransition->statusTo instanceof WorkflowStatus
        ) {
            return $places;
        }
        $places[] = $startTransition->statusFrom->state_value;
        $places[] = $startTransition->statusTo->state_value;
        foreach ($otherTransitions as $transition) {
            if (
                !$transition->statusFrom instanceof WorkflowStatus ||
                !$transition->statusTo instanceof WorkflowStatus
            ) {
                throw new ApplicationServiceException('Некорректно заполнены данные переходов.');
            }
            $places[] = $transition->statusFrom->state_value;
            $places[] = $transition->statusTo->state_value;
        }
        return array_unique($places);
    }

    /**
     * @inheritdoc
     */
    public function getPrivileges()
    {
        return [
            'getWorkflowList' => ConfigModule::t('workflow', 'Get workflows'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function aclAlias()
    {
        return ConfigModule::t('workflow', 'Workflow');
    }
}
