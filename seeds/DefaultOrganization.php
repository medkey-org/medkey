<?php
namespace app\seeds;

use app\common\seeds\Seed;
use app\modules\organization\models\orm\Organization;

class DefaultOrganization extends Seed
{
    public function run()
    {
        $this->model = Organization::class;
        $this->data = [
            [
                'title' => 'Default Organization',
                'short_title' => 'Default Organization',
                'description' => '',
            ],
        ];
    }
}