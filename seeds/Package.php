<?php
namespace app\seeds;

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
        $this->call('dashboard_item' . ucwords(substr(\Yii::$app->language,0,2)));
        $this->call('default_organization');
        $this->call('default_department');
        $this->call('default_insurance');
        $this->call('default_position');
        $this->call('default_speciality');
        $this->call('default_service');
        $this->call('default_cabinet');
        $this->call('workflow_order');
        $this->call('workflow_status_order');
        $this->call('workflow_transition_order');
        $this->call('default_patients');
        $this->call('default_ehr');
        $this->call('default_referral');
        $this->call('default_referral_item');
        $this->call('default_workplan');
        $this->call('default_workplan_weeks');
        $this->call('settings_seed');
    }
}
