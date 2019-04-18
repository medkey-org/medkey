<?php
namespace app\modules\medical\models\orm;

use app\common\db\ActiveRecord;
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
            [ ['title', 'short_title', 'description'], 'required', ],
            [ ['title', 'short_title', 'description'], 'string', ]
        ];
    }

//    public function getEmployeeToSpeciality()
//    {
//        return $this->hasMany(EmployeeToSpeciality::class, ['speciality_id' => 'id']);
//    }

    public function getEmployees()
    {
        return $this->hasMany(Employee::class, ['speciality_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabelsOverride()
    {
        return [
            'title' => 'Название',
            'short_title' => 'Короткое название',
            'description' => 'Описание',
        ];
    }
}
