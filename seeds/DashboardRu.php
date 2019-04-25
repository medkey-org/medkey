<?php
namespace app\seeds;

use app\common\seeds\Seed;

class DashboardRu extends Seed
{
    public function run()
    {
        $this->model = \app\modules\dashboard\models\orm\Dashboard::class;

        $this->data = [
            [
                'key' => 'default',
                'title' => 'Рабочий стол администратора',
                'description' => 'Данный рабочий стол добавлен по умолчанию при установке системы',
                'layout' => 'two_column',
            ],
        ];
    }
}
