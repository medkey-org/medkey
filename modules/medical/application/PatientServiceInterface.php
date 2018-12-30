<?php
namespace app\modules\medical\application;

use app\common\db\ActiveRecord;
use yii\base\Model;
use yii\data\DataProviderInterface;
use app\modules\medical\models\form\Patient as PatientForm;

/**
 * Interface PatientServiceInterface
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
interface PatientServiceInterface
{
    /**
     * @param string $id
     * @return ActiveRecord
     */
    public function getPatientById($id);
    /**
     * @param Model $form
     * @return DataProviderInterface
     */
    public function getPatientList(Model $form);
    /**
     * @param PatientForm $form
     * @return ActiveRecord
     */
    public function addPatient(PatientForm $form);
    /**
     * @param $id
     * @param PatientForm $patientForm
     * @return ActiveRecord
     */
    public function updatePatient(string $id, PatientForm $patientForm);
    public function getPatientForm($raw);
}
