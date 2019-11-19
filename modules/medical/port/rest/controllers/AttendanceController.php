<?php
namespace app\modules\medical\port\rest\controllers;

use app\common\dto\Dto;
use app\common\filters\QueryParamAuth;
use app\common\rest\Controller;
use app\common\widgets\ActiveForm;
use app\modules\medical\models\orm\Attendance;
use app\modules\medical\models\form\Attendance as AttendanceForm;
use app\modules\medical\application\AttendanceServiceInterface;
use yii\base\Module;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

/**
 * Class AttendanceController
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class AttendanceController extends Controller
{
    /**
     * @var AttendanceServiceInterface
     */
    public $attendanceService;

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
     * AttendanceController constructor.
     * @param string $id
     * @param Module $module
     * @param AttendanceServiceInterface $attendanceService
     * @param array $config
     */
    public function __construct($id, Module $module, AttendanceServiceInterface $attendanceService, array $config = [])
    {
        $this->attendanceService = $attendanceService;
        parent::__construct($id, $module, $config);
    }

    public function actionCreateBySchedule()
    {
        $dto = Dto::make(\Yii::$app->request->getBodyParams());
        return $this->attendanceService->createAttendanceBySchedule($dto);
    }

    public function actionCreateByPatientSchedule()
    {
        $params = \Yii::$app->request->getBodyParams();
        return $this->attendanceService->createAttendanceByPatientSchedule($params);
    }

    public function actionCancelBySchedule($attendanceId, $referralId = '')
    {
        return $this->attendanceService->cancelAttendance($attendanceId, $referralId);
    }

    public function actionValidateCreate()
    {
        $model = new AttendanceForm();
        $model->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson(ActiveForm::validate($model));
    }

    public function actionValidateUpdate($id = null)
    {
        $model = new AttendanceForm();
        $model->load(\Yii::$app->getRequest()->getBodyParams());
        return $this->asJson(ActiveForm::validate($model));
    }

    public function actionCreate()
    {
        $model = new Attendance();
        $model->load(\Yii::$app->getRequest()->getBodyParams());
        $model->save();
        return $this->asJson($model);
    }

    public function actionUpdate($id)
    {
        $model = Attendance::findOneEx($id);
        $model->load(\Yii::$app->getRequest()->getBodyParams());
        $model->save();
        return $this->asJson($model);
    }

    public function actionDelete($id)
    {
        $model = Attendance::ensure($id);
        return $model->delete();
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
}
