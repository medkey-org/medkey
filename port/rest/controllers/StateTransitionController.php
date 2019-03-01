<?php
namespace app\port\rest\controllers;

use app\common\filters\QueryParamAuth;
use app\common\rest\Controller;
use app\common\workflow\WorkflowManagerInterface;
use app\common\workflow\WorkflowParamsDto;
use yii\base\Module;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;

/**
 * Class StateTransitionController
 * @package Common\REST
 * @copyright 2012-2019 Medkey
 */
class StateTransitionController extends Controller
{
    /**
     * @var WorkflowManagerInterface
     */
    public $workflowManager;

    /**
     * StateTransitionController constructor.
     * @param string $id
     * @param Module $module
     * @param WorkflowManagerInterface $workflowManager
     * @param array $config
     */
    public function __construct($id, Module $module, WorkflowManagerInterface $workflowManager, array $config = [])
    {
        $this->workflowManager = $workflowManager;
        parent::__construct($id, $module, $config);
    }

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

    public function actionApply()
    {
        $params = \Yii::$app->request->getBodyParams();
        if (
            empty($params['transitionName'])
            || empty($params['ormClass'])
            || empty($params['ormId'])
            || empty($params['ormModule'])
        ) {
            throw new HttpException(400, 'Not all required fields are filled.');
        }
        $dto = new WorkflowParamsDto(
            $params['ormModule'],
            $params['ormClass'],
            $params['ormId'],
            $params['transitionName']
        );
        try {
            return $this->asJson($this->workflowManager->applyTransition($dto));
        } catch (\Exception $e) {
            throw new HttpException(400, $e->getMessage());
        }
    }
}
