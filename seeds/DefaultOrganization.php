<?php
namespace app\seeds;

use app\common\seeds\Seed;
use app\modules\organization\models\orm\Organization;

/**
 * Class AclRoleSeed
 * @package Seed
 * @copyright 2012-2019 Medkey
 */
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