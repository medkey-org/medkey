<?php
namespace app\modules\medical\port\rest\controllers;

use app\common\filters\QueryParamAuth;
use app\common\web\Controller;
use app\modules\medical\application\ScheduleServiceInterface;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

class ScheduleController extends Controller
{
    /**
     * @var ScheduleServiceInterface
     */
    public $scheduleService;

    public function __construct($id, $module, ScheduleServiceInterface $scheduleService, $config = [])
    {
        $this->scheduleService = $scheduleService;
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
    public function actionGetSchedule($date, $specialityId = null, $serviceId = null)
    {
        return $this->asJson($this->scheduleService->getSchedule($date, $specialityId, $serviceId));
    }
}
