<?php
namespace app\seeds;

use app\common\seeds\Seed;
use app\modules\security\models\orm\AclRole;

class AclRoleSeed extends Seed
{
    public function run()
    {
        $this->model = AclRole::class;

        $this->data = [
            [
                'name' => 'admin',
                'short_name' => 'admin',
                'description' => 'admin',
            ],
        ];
    }
}
