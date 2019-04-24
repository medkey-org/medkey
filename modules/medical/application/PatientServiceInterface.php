<?php
namespace app\modules\medical\application;

use app\common\db\ActiveRecord;
use yii\base\Model;
use yii\data\DataProviderInterface;
use app\modules\medical\models\form\Patient as PatientForm;
use yii\db\ActiveRecordInterface;

/**
 * Interface PatientServiceInterface
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
interface PatientServiceInterface
{
    /**
     * @param string $id
     * @return ActiveRecordInterface
     */
    public function getPatientById($id): ActiveRecordInterface;
    /**
     * @param Model $form
     * @return DataProviderInterface
     */
    public function getPatientList(Model $form): DataProviderInterface;
    /**
     * @param PatientForm $form
     * @return ActiveRecordInterface
     */
    public function addPatient(PatientForm $form): ActiveRecordInterface;
    /**
     * @param $id
     * @param PatientForm $patientForm
     * @return ActiveRecordInterface
     */
    public function updatePatient(string $id, PatientForm $patientForm): ActiveRecordInterface;
    public function getPatientForm($raw);
}
