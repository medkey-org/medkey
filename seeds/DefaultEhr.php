<?php
namespace app\seeds;

use app\common\helpers\ArrayHelper;
use app\common\seeds\Seed;
use app\modules\medical\models\orm\Ehr;
use app\modules\medical\models\orm\Patient;

class DefaultEhr extends Seed
{
    public function run()
    {
        $defaultPatients = $this->call('default_patients')->models;
        $this->model = Ehr::class;
        $this->data = [
            [
                'number' => '1',
                'type' => Ehr::TYPE_AMBULATORY,
                'status' => Ehr::STATUS_ACTIVE,
                'patient_id' => ArrayHelper::findBy($defaultPatients, [
                    'first_name' => 'John',
                    'last_name' => 'Smith',
                    'birthday' => '1975-02-02',
                    'sex' => Patient::SEX_MALE,
                ])->id
            ],
            [
                'number' => '2',
                'type' => Ehr::TYPE_AMBULATORY,
                'status' => Ehr::STATUS_ACTIVE,
                'patient_id' => ArrayHelper::findBy($defaultPatients, [
                    'first_name' => 'Alice',
                    'last_name' => 'Thomson',
                    'birthday' => '1990-04-04',
                    'sex' => Patient::SEX_FEMALE,
                ])->id,
            ]
        ];
    }
}
