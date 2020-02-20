<?php
namespace app\modules\medical\models\orm;

use app\common\db\ActiveRecord;
use app\modules\crm\models\orm\EmployeeToSpeciality;
use app\modules\medical\MedicalModule;
use app\modules\organization\models\orm\Employee;

/**
 * Class Speciality
 *
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class Speciality extends ActiveRecord
{
    public static function modelIdentity()
    {
        return [
            'title',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ 'title', 'required' ],
            [ ['title', 'short_title'], 'required' ],
            [ ['title', 'short_title', 'description'], 'string' ]
        ];
    }

//    public function getEmployeeToSpeciality()
//    {
//        return $this->hasMany(EmployeeToSpeciality::class, ['speciality_id' => 'id']);
//    }

//    public function getEmployees()
//    {
//        return $this->hasMany(Employee::class, ['speciality_id' => 'id']);
//    }

    public function getEmployeeToSpeciality()
    {
        return $this->hasMany(EmployeeToSpeciality::class, ['speciality_id' => 'id']);
    }

//    public function getSpeciality()
//    {
//        return $this->hasOne(Speciality::class, ['id' => 'speciality_id'])
//            ->via('employeeToSpeciality');
//    }

    public function getEmployees()
    {
        return $this->hasMany(Employee::class, ['id' => 'employee_id'])
            ->via('employeeToSpeciality');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabelsOverride()
    {
        return [
            'title' => MedicalModule::t('speciality', 'Title'),
            'short_title' => MedicalModule::t('speciality', 'Short title'),
            'description' => MedicalModule::t('speciality', 'Description'),
        ];
    }
}
