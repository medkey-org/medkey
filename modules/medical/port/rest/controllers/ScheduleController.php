<?php
namespace app\modules\medical\port\rest\controllers;

use app\common\filters\QueryParamAuth;
use app\common\helpers\ArrayHelper;
use app\common\web\Controller;
use app\modules\medical\application\AttendanceServiceInterface;
use app\modules\medical\application\ScheduleServiceInterface;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

class ScheduleController extends Controller
{
    public $scheduleService;
    public $attendanceService;
    public $enableCsrfValidation = false;

    public function __construct($id, $module, ScheduleServiceInterface $scheduleService, AttendanceServiceInterface $attendanceService, $config = [])
    {
        $this->scheduleService = $scheduleService;
        $this->attendanceService = $attendanceService;
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

    // todo swagger
    // response cabinet_id to schedules
    public function actionGetSchedule()
    {
        $data = \Yii::$app->getRequest()->getBodyParams();
        return $this->asJson($this->scheduleService->getSchedule($data['date'], ArrayHelper::getColumn($data['specialityIds'], 'value'), $data['serviceIds']));
    }
}
