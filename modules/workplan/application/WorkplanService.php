<?php
namespace app\modules\workplan\application;

use app\common\base\Model;
use app\common\data\ActiveDataProvider;
use app\common\db\ActiveRecord;
use app\common\helpers\ArrayHelper;
use app\common\helpers\CommonHelper;
use app\common\helpers\Json;
use app\common\service\ApplicationService;
use app\common\service\exception\AccessApplicationServiceException;
use app\common\service\exception\ApplicationServiceException;
use app\modules\medical\application\AttendanceServiceInterface;
use app\modules\medical\models\orm\Attendance;
use app\modules\workplan\models\finders\WorkplanFilter;
use app\modules\workplan\models\orm\Workplan;
use app\modules\workplan\models\orm\WorkplanToWeek;
use app\modules\workplan\models\form\Workplan as WorkplanForm;
use app\modules\workplan\WorkplanModule;

/**
 * Class WorkplanService
 * @package Module\Workplan
 * @copyright 2012-2019 Medkey
 */
class WorkplanService extends ApplicationService implements WorkplanServiceInterface
{
    /**
     * @var AttendanceServiceInterface
     */
    private $attendanceService;

    public function __construct(AttendanceServiceInterface $attendanceService, array $config = [])
    {
        $this->attendanceService = $attendanceService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function aclAlias()
    {
        return WorkplanModule::t('workplan', 'Workplan');
    }

    /**
     * @inheritdoc
     */
    public function getPrivileges()
    {
        return [
            'addWorkplan' => WorkplanModule::t('workplan', 'Add workplan'),
            'updateWorkplan' => WorkplanModule::t('workplan', 'Update workplan'),
            'getWorkplanList' => WorkplanModule::t('workplan', 'Get workplans list'),
        ];
    }

    /**
     * @param mixed $raw
     * @param string $scenario
     * @return WorkplanForm
     */
    public function getWorkplanForm($raw, $scenario = 'create')
    {
        $orm = Workplan::ensureWeak($raw);
        $form = new WorkplanForm();
        if ($orm->isNewRecord) {
            $form->setScenario('create');
        }
        $form->setScenario($scenario);
        $form->loadAr($orm);
        $form->employee = ArrayHelper::toArray($orm->employee);
        $form->id = $orm->id;
        return $form;
    }

    /**
     * @inheritdoc
     */
    public function getWorkplanList(Model $form)
    {
        /** @var $form WorkplanFilter */
        if (!$this->isAllowed('getWorkplanList')) {
            throw new AccessApplicationServiceException('Доступ к списку рабочих планов запрещен.');
        }
        $query = Workplan::find();
        $query
            ->andFilterWhere([
                'employee_id' => $form->employeeId,
                'cast(updated_at as date)' =>
                    empty($form->updatedAt) ? null : \Yii::$app->formatter->asDate($form->updatedAt, CommonHelper::FORMAT_DATE_DB),
            ]);
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function addWorkplan(WorkplanForm $form, $scenario = ActiveRecord::SCENARIO_CREATE)
    {
        $workplan = new Workplan([
            'scenario' => ActiveRecord::SCENARIO_CREATE,
        ]);
        $workplan->loadForm($form);
        if (!$workplan->save()) {
            throw new ApplicationServiceException('Не удалось сохранить полис, причина: ' . Json::encode($workplan->getErrors()));
        }
        $this->saveWeeks($workplan->id, $form['weekIds']);
        return $workplan;
    }

    /**
     * @inheritdoc
     */
    public function updateWorkplan($id, WorkplanForm $form, $scenario = ActiveRecord::SCENARIO_UPDATE)
    {
        $workplan = Workplan::findOneEx($id);
        $workplan->setScenario($scenario);
        $workplan->loadForm($form);
        if (!$workplan->save()) {
            throw new ApplicationServiceException('Не удалось сохранить полис, причина: ' . Json::encode($workplan->getErrors()));
        }
        $this->saveWeeks($workplan->id, $form['weekIds']);
        return $workplan;
    }

    private function saveWeeks($workplanId, $weekIds)
    {
        $workplan = Workplan::findOneEx($workplanId);
        WorkplanToWeek::deleteAll(['workplan_id' => $workplanId]);
//        $workplan->unlinkAll('workplanToWeeks'); // todo история теряется
        // TODO удалить полностью, а не анлинкать!!!!
        if (empty($weekIds) || !is_array($weekIds)) {
            return null;
        }
        foreach ($weekIds as $week) {
            $workplanToWeek = new WorkplanToWeek();
            $workplanToWeek->week = $week;
            $workplanToWeek->save();
            $workplan->link('workplanToWeeks', $workplanToWeek);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getWorkplansByExistsRules($employeeId, $date)
    {
        $w = [];
        $workplans = Workplan::find()
            ->where([
                'employee_id' => $employeeId,
                'status' => Workplan::STATUS_ACTIVE
            ])
            ->andWhere([
                '<=',
                'since_date',
                \Yii::$app->formatter->asDate($date, CommonHelper::FORMAT_DATE_DB),
            ])->andWhere([
                '>=',
                'expire_date',
                \Yii::$app->formatter->asDate($date, CommonHelper::FORMAT_DATE_DB),
            ])
            ->notDeleted()
            ->all();
        if (!empty($workplans)) {
            foreach ($workplans as $workplan) {
                $weeks = $workplan->weekIds;
                if ($this->checkDayInWeeks($weeks, $date)) {
                    array_push($w, $workplan);
                }
                // todo проверка в будущем на rules таблицы workplan и workplan_to_week
                // todo проверка на таблицу workplan_to_exclusion
            }
        }
        return $w;
    }

    public function getScheduleMedworkerTimes($employeeId, $date)
    {
        $workplans = $this->getWorkplansByExistsRules($employeeId, $date);
        if(empty($workplans)) {
            return [];
        }

        $attendances = $this->attendanceService->getAttendancesByEmployeeIdAndDate($employeeId, $date);


        $duration = Attendance::ATTENDANCE_DURATION; // seconds
        $scheduleTimes = []; // cabinet_id to times
        foreach ($workplans as $workplan) {
            $sinceTimeTs = (int)\Yii::$app->formatter->asTimestamp($workplan->since_time . date_default_timezone_get());
            $expireTimeTs = (int)\Yii::$app->formatter->asTimestamp($workplan->expire_time . date_default_timezone_get());
            $delta = $expireTimeTs - $sinceTimeTs;
            $i = 0;
            $scheduleTimes[$workplan->cabinet->number] = [];
            while ($delta > 0) {
                array_push($scheduleTimes[$workplan->cabinet->number], \Yii::$app->formatter->asTime($sinceTimeTs, CommonHelper::FORMAT_TIME_UI));
                $sinceTimeTs = $sinceTimeTs + $duration;
                $delta = $delta - $duration;
                $i++;
            }

            $scheduleTimes[$workplan->cabinet->number] = array_unique($scheduleTimes[$workplan->cabinet->number]);
        }
        return $scheduleTimes;
    }

    public function getScheduleMedworkerTimesWithAttendances($employeeId, $date)
    {
        $date = \Yii::$app->formatter->asDate($date, CommonHelper::FORMAT_DATE_DB); // format date from frontend
        $workplans = $this->getWorkplansByExistsRules($employeeId, $date);

        // готовим одним пакетом записи для отображения в расписании
        $attendances = $this->attendanceService->getAttendancesByEmployeeIdAndDate($employeeId, $date);
        $attendanceMap = [];
        foreach ($attendances as $value) {
            $attendanceMap[\Yii::$app->formatter->asTimestamp($value['datetime'] . date_default_timezone_get())] = $value;
        }
        $duration = Attendance::ATTENDANCE_DURATION; // seconds
        $scheduleTimes = []; // cabinet_id to times
        foreach ($workplans as $workplan) {
            $sinceTimeTs = (int)\Yii::$app->formatter->asTimestamp($workplan->since_time . date_default_timezone_get());
            $expireTimeTs = (int)\Yii::$app->formatter->asTimestamp($workplan->expire_time . date_default_timezone_get());
            $delta = $expireTimeTs - $sinceTimeTs;
            $i = 0;
            $scheduleTimes[$workplan->cabinet->number] = [];
            while ($delta > 0) {
                $time = \Yii::$app->formatter->asTime($sinceTimeTs, CommonHelper::FORMAT_TIME_UI);
                $prepare = [
                    'time' => $time,
                ];
                $dt = \Yii::$app->formatter->asTimestamp($date . ' ' . $time . date_default_timezone_get()); // current datetime string

                if (isset($attendanceMap[$dt])) {
                    $prepare['attendance_id'] = $attendanceMap[$dt]['id'];
                    if (isset($attendanceMap[$dt]['ehr']) && isset($attendanceMap[$dt]['ehr']['patient'])) {
                        $prepare['patientFullName'] = $attendanceMap[$dt]['ehr']['patient']['fullName'];
                    }
                }

                array_push(
                    $scheduleTimes[$workplan->cabinet->number],
                        $prepare
                );
                $sinceTimeTs = $sinceTimeTs + $duration;
                $delta = $delta - $duration;
                $i++;
            }
//            $scheduleTimes[$workplan->cabinet->number] = array_unique($scheduleTimes[$workplan->cabinet->number]);
        }
        return $scheduleTimes;
    }

    private function checkDayInWeeks($weeks, $date)
    {
        $day = \Yii::$app->formatter->asDate($date, CommonHelper::FORMAT_DATE_DB);
        $w1 = (int)\Yii::$app->formatter->asDate($day, 'e');
        if (in_array($w1, $weeks)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $day
     * @param array|string $rules
     *
     * @return bool
     * @todo реализовать согласно todo в методе [[getWorkplansByExistsRules()]]
     */
    private function checkDayInWorkplanRules($day, $rules)
    {
        return true;
    }

    /**
     * @param string $day
     * @param ActiveRecord[] $exclusions
     *
     * @return bool
     * @todo реализовать согласно todo в методе [[getWorkplansByExistsRules()]]
     */
    private function checkDayInWorkplanExclusion($day, $exclusions)
    {
        return true;
    }
}
