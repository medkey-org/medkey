<?php
namespace app\seeds\install;

use app\common\seeds\Seed;

/**
 * Seed with default dashboard
 * @package Seed
 * @copyright 2012-2019 Medkey
 */
class Dashboard extends Seed
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
