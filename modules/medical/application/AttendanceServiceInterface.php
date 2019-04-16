<?php
namespace app\modules\medical\application;

use app\common\dto\Dto;

/**
 * Interface AttendanceServiceInterface
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
interface AttendanceServiceInterface
{
    /**
     * @param string|integer $id
     * @return \app\modules\medical\models\orm\Attendance
     */
    public function getAttendanceById($id);

    /**
     * @param string|integer $ehrId
     * @param string|integer $employeeId
     * @param $datetime
     * @return boolean
     */
    public function checkRecordByDatetime(string $ehrId, string $employeeId, $datetime);

    /**
     * @param string|integer $attendanceId
     * @param string|integer $referralId
     */
    public function cancelAttendance(string $attendanceId, string $referralId);

    /**
     * @param \yii\base\Model $form
     * @return \app\modules\medical\models\orm\Attendance[]
     */
    public function getAttendanceList(\yii\base\Model $form);

    /**
     * @param Dto $dto
     * @return \app\modules\medical\models\orm\Attendance
     */
    public function createAttendanceBySchedule(Dto $dto);
}
