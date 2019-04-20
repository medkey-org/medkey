<?php
namespace app\seeds;

use app\common\helpers\ArrayHelper;
use app\common\seeds\Seed;
use app\modules\organization\models\orm\Employee;

class EmployeeSeed extends Seed
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $users = $this->call('user')->models;
        $defaultSpeciality = $this->call('default_speciality')->models;
        $this->model = Employee::class;
        $this->data = [
            [
                'user_id' => ArrayHelper::findBy($users, ['login' => 'admin'])->id,
                'first_name' => 'administrator',
                'middle_name' => 'administrator',
                'last_name' => 'administrator',
                'birthday' => '1970-01-01',
                'sex' => Employee::SEX_MALE,
                'speciality_id' => ArrayHelper::findBy($defaultSpeciality, ['title' => 'Default Doctor'])->id,
            ]
        ];
    }
}
