<?php
namespace app\modules\medical\application;

use app\common\data\ActiveDataProvider;
use app\common\dto\Dto;
use app\modules\medical\models\orm\Attendance;
use yii\data\DataProviderInterface;

/**
 * Interface AttendanceServiceInterface
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
interface AttendanceServiceInterface
{
    /**
     * @param string|integer $id
     * @return Attendance
     */
    public function getAttendanceById($id) : ?Attendance;

    /**
     * @param string|integer $ehrId
     * @param string|integer $employeeId
     * @param $datetime
     * @return string Record Id or empty string
     */
    public function getAttendanceByEhrIdAndEmployeeIdAndDatetime(string $ehrId, string $employeeId, $datetime) : ?Attendance;

    /**
     * @param string|integer $attendanceId
     * @param string|integer $referralId
     * @return Attendance
     */
    public function cancelAttendance(string $attendanceId, string $referralId) : Attendance;

    /**
     * @param \yii\base\Model $form
     * @return ActiveDataProvider
     */
    public function getAttendanceList(\yii\base\Model $form): DataProviderInterface;

    /**
     * @param Dto $dto
     * @return \app\modules\medical\models\orm\Attendance
     */
    public function createAttendanceBySchedule(Dto $dto): Attendance;
}
