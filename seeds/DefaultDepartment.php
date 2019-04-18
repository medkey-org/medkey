<?php
namespace app\seeds;

use app\common\helpers\ArrayHelper;
use app\common\seeds\Seed;
use app\modules\organization\models\orm\Department;

/**
 * Class AclRoleSeed
 * @package Seed
 * @copyright 2012-2019 Medkey
 */
class DefaultDepartment extends Seed
{
    public function run()
    {
        $organizations = $this->call('default_organization')->models;
        $this->model = Department::class;
        $this->data = [
            [
                'organization_id' => ArrayHelper::findBy($organizations, ['title' => 'Default Organization'])->id,
                'title' => 'Default Department',
                'short_title' => 'Default Department',
            ]
        ];
    }
}
