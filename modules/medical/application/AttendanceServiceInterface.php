<?php
namespace app\modules\medical\application;

use app\common\dto\Dto;
use yii\base\Model;

/**
 * Interface AttendanceServiceInterface
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
interface AttendanceServiceInterface
{
    public function getAttendanceById($id);
    public function checkRecordByDatetime(string $ehrId, string $employeeId, $datetime);
    public function cancelAttendance(string $attendanceId, string $referralId);
    public function getAttendanceList(Model $form);
    public function createAttendanceBySchedule(Dto $dto);
}
