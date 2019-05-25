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

    public function getSchedule(string $date, string $specialityId = null, string $serviceId = null)
    {
        // TODO status filter
        $employees = Employee::find()
            ->notDeleted()
            ->andFilterWhere([
                '[[employee]].[[speciality_id]]' => $specialityId,
            ])
            ->all();

        $result = [];
        foreach($employees as $employee) {
            array_push(
                $result,
                array_merge(
                    $employee->toArray(),
                    ['schedule' => $this->workplanService->getScheduleMedworkerTimes($employee->id, $date)]
                )
            );
        }

        return $result;
    }
}
