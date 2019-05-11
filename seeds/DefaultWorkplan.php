<?php
namespace app\seeds;

use app\common\helpers\ArrayHelper;
use app\common\seeds\Seed;
use app\modules\organization\models\orm\Employee;
use app\modules\workplan\models\orm\Workplan;

class DefaultWorkplan extends Seed
{
    public function run()
    {
        $employee = $this->call('employee_seed')->models;
        $defaultCabinet = $this->call('default_cabinet')->models;
//        $defaultDepartment = $this->call('default_department')->models;
        $this->model = Workplan::class;
        $this->data = [
            [
                'since_date' => '2019-01-01',
                'expire_date' => '2023-01-01',
                'since_time' => '10:00:00',
                'expire_time' => '15:00:00',
                'employee_id' => ArrayHelper::findBy($employee, [
                    'first_name' => 'administrator',
                    'last_name' => 'administrator',
                    'birthday' => '1970-01-01',
                    'sex' => Employee::SEX_MALE
                ])->id,
                'cabinet_id' => 1, // TODO key
                'department_id' => 1, // TODO key
                'status' => Workplan::STATUS_ACTIVE,
            ],
        ];
    }
}
