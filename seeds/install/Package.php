<?php
namespace app\seeds\install;

use app\common\seeds\Seed;

/**
 * Package
 * @package Seed
 * @copyright 2012-2019 Medkey
 */
class Package extends Seed
{
    public function run()
    {
        $this->call('acl');
        $this->call('user');
        $this->call('employee_seed');
        $this->call('dashboard_item');
    }
}
