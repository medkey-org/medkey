<?php
namespace app\seeds;

use app\common\helpers\ArrayHelper;
use app\common\helpers\CommonHelper;
use app\common\seeds\Seed;
use app\modules\organization\models\orm\Employee;

/**
 * Class EmployeeSeed
 * @package Seed
 * @copyright 2012-2019 Medkey
 */
class EmployeeSeed extends Seed
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        $users = $this->call('user')->models;
        $this->model = Employee::class;
        $this->data = [
            [
                'user_id' => ArrayHelper::findBy($users, ['login' => 'admin'])->id,
                'first_name' => 'administrator',
                'middle_name' => '',
                'last_name' => 'administrator',
                'birthday' => \Yii::$app->formatter->asDate(time(), CommonHelper::FORMAT_DATE_DB),
                'sex' => Employee::SEX_MALE,
            ]
        ];
    }
}
