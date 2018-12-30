<?php
namespace app\modules\config\port\rest\controllers;

use app\common\filters\QueryParamAuth;
use app\common\rest\Controller;
use app\common\widgets\ActiveForm;
use app\modules\config\application\WorkflowStatusServiceInterface;
use app\modules\config\models\form\WorkflowStatus as WorkflowStatusForm;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

/**
 * Class WorkflowStatusController
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class WorkflowStatusController extends Controller
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
     * @var WorkflowStatusServiceInterface
     */
    public $workflowStatusService;

    public function __construct($id, $module, WorkflowStatusServiceInterface $workflowStatusManager, array $config = [])
    {
        $this->workflowStatusService = $workflowStatusManager;
        parent::__construct($id, $module, $config);
    }

    public function actionCreate()
    {
        $workflowStatusForm = new WorkflowStatusForm([
            'scenario' => 'create',
        ]);
        $workflowStatusForm->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson($this->workflowStatusService->createWorkflowStatus($workflowStatusForm));
    }

    public function actionValidateCreate()
    {
        $workflowForm = new WorkflowStatusForm([
            'scenario' => 'create'
        ]);
        $workflowForm->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson(ActiveForm::validate($workflowForm));
    }

    public function actionUpdate($id)
    {
        $workflowStatusForm = new WorkflowStatusForm([
            'scenario' => 'create',
        ]);
        $workflowStatusForm->load(\Yii::$app->getRequest()->getBodyParams());
        $workflowStatusForm->id = $id;
        return $this->asJson($this->workflowStatusService->updateWorkflowStatus($workflowStatusForm));
    }

    public function actionValidateUpdate($id)
    {
        $workflowForm = new WorkflowStatusForm([
            'scenario' => 'update'
        ]);
        $workflowForm->load(\Yii::$app->getRequest()->getBodyParams());
        $workflowForm->id = $id;
        return $this->asJson(ActiveForm::validate($workflowForm));
    }
}
