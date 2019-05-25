<?php
namespace app\modules\organization\port\rest\controllers;

use app\common\base\Module;
use app\common\data\ActiveDataProvider;
use app\common\filters\QueryParamAuth;
use app\common\rest\Controller;
use app\common\widgets\ActiveForm;
use app\modules\organization\application\EmployeeServiceInterface;
use app\modules\organization\models\orm\Employee;
use yii\db\ActiveRecordInterface;
use app\modules\organization\models\form\Employee as EmployeeForm;
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
     * @var string the model class name. This property must be set.
     */
    public $modelClass = Employee::class;
    /**
     * @var EmployeeServiceInterface
     */
    private $employeeService;

    /**
     * EmployeeController constructor.
     * @param string $id
     * @param Module $module
     * @param EmployeeServiceInterface $manager
     * @param array $config
     */
    public function __construct($id, $module, EmployeeServiceInterface $manager, array $config = [])
    {
        $this->employeeService = $manager;
        parent::__construct($id, $module, $config);
    }

    /**
     * {@inheritDoc}
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
     * @inheritdoc
     */
    public function actions()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function verbs()
    {
        return [];
    }

    public function actionIndex($q)
    {
        /* @var $modelClass ActiveRecordInterface */
        $modelClass = $this->modelClass;
        $query = $modelClass::find()
            ->where([
                'like',
                'last_name',
                $q
            ])
            ->orWhere([
                'like',
                'first_name',
                $q
            ])
            ->orWhere([
                'like',
                'middle_name',
                $q
            ]);
        /** @var ActiveDataProvider $provider */
        $provider = \Yii::createObject([
            'class' => ActiveDataProvider::class,
            'query' => $query
        ]);
        return $provider;
    }

    public function actionCreate()
    {
        $employeeForm = new EmployeeForm([
            'scenario' => 'create',
        ]);
        $employeeForm->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson($this->employeeService->addEmployee($employeeForm));
    }

    public function actionUpdate($id)
    {
        $employeeForm = new EmployeeForm([
            'scenario' => 'update'
        ]);
        $employeeForm->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson($this->employeeService->updateEmployee($id, $employeeForm));
    }

    public function actionValidateCreate()
    {
        $form = new EmployeeForm([
            'scenario' => 'create'
        ]);
        $form->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson(ActiveForm::validate($form));
    }

    public function actionValidateUpdate()
    {
        $form = new EmployeeForm([
            'scenario' => 'update'
        ]);
        $form->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson(ActiveForm::validate($form));
    }

    public function actionEmployeesWithAttendanceByDate($date)
    {
        $employees = $this->employeeService->getEmployeesWithAttendanceByDate($date);
        $result = [];
        foreach ($employees as $employee) {
            /** @var Employee $employee */
            $result[] = $employee->toArray([], ['speciality', 'attendances.ehr.patient']);
        }
        return $this->asJson($result);
    }
}
