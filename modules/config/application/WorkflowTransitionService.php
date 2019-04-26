<?php
namespace app\modules\config\application;

use app\common\data\ActiveDataProvider;
use app\common\helpers\Json;
use app\common\service\ApplicationService;
use app\common\service\exception\AccessApplicationServiceException;
use app\common\service\exception\ApplicationServiceException;
use app\modules\config\ConfigModule;
use app\modules\config\models\finders\WorkflowTransitionFinder;
use app\modules\config\models\orm\WorkflowTransition;
use app\modules\config\models\form\WorkflowTransition as WorkflowTransitionForm;

/**
 * Class WorkflowTransitionService
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class WorkflowTransitionService extends ApplicationService implements WorkflowTransitionServiceInterface
{
    /**
     * @inheritdoc
     */
    public function getWorkflowTransitionForm($raw)
    {
        $workflowTransition = WorkflowTransition::ensureWeak($raw);
        $workflowTransitionForm = new WorkflowTransitionForm();
        $workflowTransitionForm->loadAr($workflowTransition);
        $workflowTransitionForm->id = $workflowTransition->id;
        return $workflowTransitionForm;
    }

    /**
     * @param WorkflowTransitionFinder $form
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidConfigException
     */
    public function getWorkflowTransitionList(WorkflowTransitionFinder $form)
    {
        if (!$this->isAllowed('getWorkflowTransitionList')) {
            throw new AccessApplicationServiceException('Доступ к списку переходов workflow недоступен.');
        }
        $query = WorkflowTransition::find()
            ->andFilterWhere([
                'cast("user"."updated_at" as date)' =>
                    empty($form->updatedAt) ? null : \Yii::$app->formatter->asDate($form->updatedAt, CommonHelper::FORMAT_DATE_DB),
                WorkflowTransition::tableColumns('workflow_id') => $form->workflowId
            ])->notDeleted();
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
     * @param WorkflowTransitionForm $form
     * @return WorkflowTransition
     */
    public function createWorkflowTransition(WorkflowTransitionForm $form)
    {
        $workflowTransition = new WorkflowTransition();
        $workflowTransition->loadForm($form);
        if (!$workflowTransition->save()) {
            throw new ApplicationServiceException('Не удалось создать переход ЖЦ. Причина: ' . Json::encode($workflowTransition->getErrors()));
        }
        return $workflowTransition;
    }

    /**
     * @param WorkflowTransitionForm $form
     * @return mixed
     */
    public function updateWorkflowTransition(WorkflowTransitionForm $form)
    {
        $workflowTransition = WorkflowTransition::findOneEx($form->id);
        $workflowTransition->loadForm($form);
        if (!$workflowTransition->save()) {
            throw new ApplicationServiceException('Не удалось обновить переход ЖЦ. Причина: ' . Json::encode($workflowTransition->getErrors()));
        }
        return $workflowTransition;
    }

    /**
     * @inheritdoc
     */
    public function getPrivileges()
    {
        return [
            'getWorkflowTransitionList' => ConfigModule::t('workflow', 'Workflow transitions list'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function aclAlias()
    {
        return ConfigModule::t('workflow', 'Workflow transition');
    }
}
