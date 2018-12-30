<?php
namespace app\modules\organization\port\ui\controllers;

use app\common\filters\QueryParamAuth;
use app\common\web\Controller;
use app\modules\organization\application\EmployeeServiceInterface;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

/**
 * Class EmployeeController
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */
class EmployeeController extends Controller
{
    /**
     * @var EmployeeServiceInterface
     */
    public $employeeService;

    public function __construct($id, $module, EmployeeServiceInterface $employeeService, array $config = [])
    {
        $this->employeeService = $employeeService;
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
    
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionView($userId = null, $id = null)
    {
        $employee = $this->employeeService->getEmployeeById($id);
        $userId = $userId ? $userId : $employee->user->id;
        return $this->render('view', [
            'modelId' => $id,
            'userId' => $userId,
        ]);
    }
}
