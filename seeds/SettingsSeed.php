<?php
namespace app\seeds;

use app\common\seeds\Seed;
use app\modules\config\models\orm\Config;

class SettingsSeed extends Seed
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->model = Config::class;
        $this->data = [
            [
                'key' => 'language',
                'value' => 'en-US',
            ],
            [
                'key' => 'application_title',
                'value' => 'Medkey',
            ]
        ];
    }
}
