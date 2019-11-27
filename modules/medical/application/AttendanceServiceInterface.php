<?php
namespace app\modules\medical\application;

use app\common\data\ActiveDataProvider;
use app\common\dto\Dto;
use app\modules\medical\models\orm\Attendance;
use yii\data\DataProviderInterface;
use yii\base\Model;

/**
 * Interface AttendanceServiceInterface
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
interface AttendanceServiceInterface
{
    public function getAttendanceById($id) : ?Attendance;
    public function getAttendanceByEhrIdAndEmployeeIdAndDatetime(string $ehrId, string $employeeId, $datetime): ?Attendance;
    public function cancelAttendance(string $attendanceId, string $referralId = ''): Attendance;
    public function getAttendanceList(Model $form): DataProviderInterface;
    public function createAttendanceBySchedule(Dto $dto): Attendance;
    public function createAttendanceByPatientSchedule($params): Attendance;
    public function getAttendancesByEmployeeIdAndDate($employeeId, $date);
}
