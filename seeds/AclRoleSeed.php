<?php
namespace app\seeds;

use app\common\seeds\Seed;
use app\modules\security\models\orm\AclRole;

/**
 * Class AclRoleSeed
 * @package Seed
 * @copyright 2012-2019 Medkey
 */
class AclRoleSeed extends Seed
{
    public function run()
    {
        $this->model = AclRole::class;

        $this->data = [
            [
                'name' => 'admin',
                'short_name' => 'adm',
                'description' => 'admin',
            ],
        ];
    }
}
