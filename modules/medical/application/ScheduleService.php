<?php
namespace app\modules\medical\application;

use app\common\service\ApplicationService;
use app\modules\organization\models\orm\Employee;
use app\modules\workplan\application\WorkplanServiceInterface;

class ScheduleService extends ApplicationService implements ScheduleServiceInterface
{
    /**
     * @var WorkplanServiceInterface
     */
    private $workplanService;

    public function __construct(WorkplanServiceInterface $workplanService, $config = [])
    {
        $this->workplanService = $workplanService;
        parent::__construct($config);
    }

    public function getSchedule(string $date, array $specialityIds = [], array $serviceIds = [])
    {
        // TODO status filter
        $employees = Employee::find()
            ->joinWith(['speciality'])
            ->notDeleted()
            ->andFilterWhere([
                '[[employee]].[[speciality_id]]' => $specialityIds,
            ])
            ->all();

        $result = [];
        foreach($employees as $employee) {
            $schedule = $this->workplanService->getScheduleMedworkerTimesWithAttendances($employee->id, $date);
            if (empty($schedule)) {
                continue;
            }
            array_push(
                $result,
                array_merge(
                    $employee->toArray([], ['speciality']),
                    ['schedule' =>$schedule]
                )
            );
        }

        return $result;
    }
}
