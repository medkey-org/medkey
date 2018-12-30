<?php
namespace app\modules\config\port\rest\controllers;

use app\common\filters\QueryParamAuth;
use app\common\rest\Controller;
use app\common\widgets\ActiveForm;
use app\modules\config\application\WorkflowTransitionServiceInterface;
use app\modules\config\models\form\WorkflowTransition as WorkflowTransitionForm;
use app\modules\config\models\orm\WorkflowTransition;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

/**
 * Class WorkflowTransitionController
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class WorkflowTransitionController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'authenticator' => [
                'class' => QueryParamAuth::class,
                'isSession' => false,
                'optional' => [
                    '*',
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function () {
                    throw new ForbiddenHttpException(\Yii::t('yii', 'You are not allowed to perform this action.'));
                }
            ],
        ]);
    }

    /**
     * @var WorkflowTransitionServiceInterface
     */
    public $workflowTransitionService;

    public function __construct($id, $module, WorkflowTransitionServiceInterface $workflowTransitionService, array $config = [])
    {
        $this->workflowTransitionService = $workflowTransitionService;
        parent::__construct($id, $module, $config);
    }

    public function actionCreate()
    {
        $workflowForm = new WorkflowTransitionForm();
        $workflowForm->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson($this->workflowTransitionService->createWorkflowTransition($workflowForm));
    }

    public function actionValidateCreate()
    {
        $workflowForm = new WorkflowTransitionForm();
        $workflowForm->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson(ActiveForm::validate($workflowForm));
    }

    public function actionUpdate($id)
    {
        $workflowTransitionForm = new WorkflowTransitionForm();
        $workflowTransitionForm->load(\Yii::$app->getRequest()->getBodyParams());
        $workflowTransitionForm->id = $id;
        return $this->asJson($this->workflowTransitionService->updateWorkflowTransition($workflowTransitionForm));
    }

    public function actionValidateUpdate($id)
    {
        $workflowTransitionForm = new WorkflowTransitionForm();
        $workflowTransitionForm->load(\Yii::$app->getRequest()->getBodyParams());
        $workflowTransitionForm->id = $id;
        return $this->asJson(ActiveForm::validate($workflowTransitionForm));
    }

    public function actionDelete($id)
    {
        $workflowTransition = WorkflowTransition::findOneEx($id);
        return $workflowTransition->deleteHistory();
    }
}
