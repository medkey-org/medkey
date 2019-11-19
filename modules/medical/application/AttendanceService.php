<?php
namespace app\modules\medical\application;

use app\common\data\ActiveDataProvider;
use app\common\db\ActiveRecord;
use app\common\dto\Dto;
use app\common\helpers\ArrayHelper;
use app\common\helpers\CommonHelper;
use app\common\helpers\Json;
use app\common\service\ApplicationService;
use app\common\service\exception\AccessApplicationServiceException;
use app\common\service\exception\ApplicationServiceException;
use app\modules\medical\MedicalModule;
use app\modules\medical\models\finders\AttendanceFilter;
use app\modules\medical\models\orm\Attendance;
use app\modules\medical\models\form\Attendance as AttendanceForm;
use app\modules\medical\models\orm\Patient;
use app\modules\medical\models\orm\Referral;
use app\modules\organization\models\orm\Employee;
use yii\base\Model;
use yii\data\DataProviderInterface;

/**
 * Class AttendanceService
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class AttendanceService extends ApplicationService implements AttendanceServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAttendanceById($id): Attendance
    {
        $model = Attendance::findOne($id);
        if (!$this->isAllowed('getAttendanceById')) {
            throw new AccessApplicationServiceException(MedicalModule::t('attendance', 'Access restricted'));
        }
        return $model;
    }

    /**
     * @todo rename into bySchedule
     * {@inheritdoc}
     */
    public function cancelAttendance(string $attendanceId, string $referralId = ''): Attendance
    {
        $attendance = Attendance::findOneEx($attendanceId);
        $attendance->setScenario(ActiveRecord::SCENARIO_UPDATE);
        $attendance->status = Attendance::STATUS_CANCEL;
        if (!$attendance->save()) {
            throw new ApplicationServiceException(MedicalModule::t('attendance', 'Can\'t save record. Reason: ') . Json::encode($attendance->getErrors()));
        }

        if (!empty($referralId)) {
            $referral = Referral::findOne($referralId);
            if ($referral) {
                $attendance->unlink('referrals', $referral, true);
            }

        }
        return $attendance;
    }

    /**
     * @todo rename into bySchedule
     * {@inheritdoc}
     */
    public function cancelAttendanceBySchedulePatient(string $attendanceId, string $referralId): Attendance
    {
//        $referral = Referral::findOneEx($referralId);
        $attendance = Attendance::findOneEx($attendanceId);
        $attendance->setScenario(ActiveRecord::SCENARIO_UPDATE);
        $attendance->status = Attendance::STATUS_CANCEL;
        if (!$attendance->save()) {
            throw new ApplicationServiceException(MedicalModule::t('attendance', 'Can\'t save record. Reason: ') . Json::encode($attendance->getErrors()));
        }
//        $attendance->unlink('referrals', $referral, true);
        return $attendance;
    }

    public function createAttendanceByPatientSchedule($params): Attendance
    {
//        if (!$this->isAllowed('createAttendanceBySchedule')) {
//            throw new AccessApplicationServiceException(MedicalModule::t('attendance', 'Access restricted'));
//        }
//        $referral = Referral::findOneEx($dto->referralId);
        if (empty($params['ehrId']) || empty($params['employeeId']) || empty($params['date']) || empty($params['time'])) {
            throw new ApplicationServiceException("error validation");
        }
        $datetime = \Yii::$app->formatter->asDate($params['date'], CommonHelper::FORMAT_DATE_DB) . ' ' . $params['time'];
        $attendance = new Attendance([
            'scenario' => ActiveRecord::SCENARIO_CREATE
        ]);
        $attendance->ehr_id = $params['ehrId'];
        $attendance->employee_id = $params['employeeId'];
        $attendance->datetime = $datetime;
//        $attendance->cabinet_id = $params['cabinetNumber'];
        if (!$attendance->save()) {
            throw new ApplicationServiceException(MedicalModule::t('attendance', 'Can\'t save record. Reason: ') . Json::encode($attendance->getErrors()));
        }
//        $attendance->link('referrals', $referral);
        return $attendance;
    }

    /**
     * @todo delete dto entity
     * {@inheritdoc}
     */
    public function createAttendanceBySchedule(Dto $dto): Attendance
    {
        if (!$this->isAllowed('createAttendanceBySchedule')) {
            throw new AccessApplicationServiceException(MedicalModule::t('attendance', 'Access restricted'));
        }
        if (!$dto instanceof Dto) {
            throw new ApplicationServiceException('param in not Dto.');
        }
        $referral = Referral::findOneEx($dto->referralId);
        $attendance = new Attendance([
            'scenario' => ActiveRecord::SCENARIO_CREATE
        ]);
        $attendance->ehr_id = $dto->ehrId;
        $attendance->employee_id = $dto->employeeId;
        $attendance->datetime = $dto->datetime;
        $attendance->cabinet_id = empty($dto->cabinetId) ?: $dto->cabinetId;
        if (!$attendance->save()) {
            throw new ApplicationServiceException(MedicalModule::t('attendance', 'Can\'t save record. Reason: ') . Json::encode($attendance->getErrors()));
        }
        $attendance->link('referrals', $referral);
        return $attendance;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttendanceForm($raw)
    {
        $model = Attendance::ensureWeak($raw);

        $ehr = ArrayHelper::toArray($model->ehr);
        $employee = ArrayHelper::toArray($model->employee);
        $attendanceForm = new AttendanceForm();
        $attendanceForm->loadAr($model);
        $attendanceForm->id = $model->id;
        $attendanceForm->ehr = $ehr;
        $attendanceForm->employee = $employee;
        return $attendanceForm;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttendanceList(Model $form): DataProviderInterface
    {
        /** @var $form AttendanceFilter */
        if (!$this->isAllowed('getAttendanceList')) {
            throw new AccessApplicationServiceException(MedicalModule::t('attendance', 'Access restricted'));
        }
        $query = Attendance::find()
            ->distinct(true)
            ->joinWith(['ehr.patient', 'cabinet'])
            ->andFilterWhere([
                Patient::tableColumns('id') => $form->patientId,
            ]);
        if (!empty($form->referralId)) {
            $query
                ->joinWith(['referrals'])
                ->andFilterWhere([
                    Referral::tableColumns('id') => $form->referralId,
                ]);
        }
        $query
            ->andFilterWhere([
                '[[attendance]].[[ehr_id]]' => $form->ehrId,
                '[[attendance]].[[employee_id]]' => $form->employeeId,
                '[[cabinet]].[[number]]' => $form->cabinetNumber,
                '[[attendance]].[[status]]' => $form->status,
                '[[attendance]].[[type]]' => $form->type,
                'cast([[attendance]].[[updated_at]] date)' =>
                    empty($form->updatedAt) ? null : \Yii::$app->formatter->asDate($form->updatedAt, CommonHelper::FORMAT_DATE_DB),
                '[[attendance]].[[datetime]]' =>
                    empty($form->datetime) ? null : \Yii::$app->formatter->asDatetime($form->datetime . date_default_timezone_get(), CommonHelper::FORMAT_DATETIME_DB)
            ]);
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => false,
//                [
//                'attributes' => [
//                    'status',
//                    'datetime',
//                    'updated_at',
//                    'ehr.number' => [
//                        'asc' => [
//                            'ehr.number' => SORT_ASC,
//                        ],
//                        'desc' => [
//                            'ehr.number' => SORT_DESC,
//                        ],
//                    ],
//                    'cabinet.number' => [
//                        'asc' => [
//                            'cabinet.number' => SORT_ASC,
//                        ],
//                        'desc' => [
//                            'cabinet.number' => SORT_DESC,
//                        ],
//                    ],
//                ],
//            ],
            'pagination' => [
                'pageSize' => 10
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getPrivileges()
    {
        return [
            'getAttendanceById' => MedicalModule::t('attendance', 'View appointment'),
            'createAttendanceBySchedule' => MedicalModule::t('attendance', 'Create appointment'),
            'cancelAttendance' => MedicalModule::t('attendance', 'Cancel appointment'),
            'getAttendanceList' => MedicalModule::t('attendance', 'View appointment list'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function aclAlias()
    {
        return MedicalModule::t('attendance', 'Appointment');
    }

    /**
     * {@inheritdoc}
     */
    public function getAttendancesByEmployeeIdAndDate($employeeId, $date)
    {
        return Attendance::find()
            ->joinWith(['ehr.patient'])
            ->notDeleted()
            ->where([
                '[[attendance]].[[employee_id]]' => $employeeId,
                'cast(datetime as DATE)' => $date,
                '[[attendance]].[[status]]' => [
                    Attendance::STATUS_NEW,
                    Attendance::STATUS_PROGRESS,
                    Attendance::STATUS_ARCHIVE,
                    Attendance::STATUS_ABSENCE,
                    Attendance::STATUS_CONFIRM
                ],
            ])
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getAttendanceByEhrIdAndEmployeeIdAndDatetime(string $ehrId, string $employeeId, $datetime): ?Attendance
    {
        $attendance = Attendance::find()
            ->notDeleted()
            ->where([
                'ehr_id' => $ehrId,
                'employee_id' => $employeeId,
                'datetime' => \Yii::$app->formatter->asDatetime($datetime . date_default_timezone_get(), CommonHelper::FORMAT_DATETIME_DB),
                'status' => [
                    Attendance::STATUS_NEW,
                    Attendance::STATUS_PROGRESS,
                    Attendance::STATUS_ARCHIVE,
                    Attendance::STATUS_ABSENCE,
                    Attendance::STATUS_CONFIRM
                ],
            ])
            ->one();
        return $attendance;
    }
}
