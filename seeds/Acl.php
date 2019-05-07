<?php
namespace app\seeds;

use app\common\helpers\ArrayHelper;
use app\common\seeds\Seed;

class Acl extends Seed
{
    public function run()
    {
        $aclRoles = $this->call('acl_role_seed')->models;
        $this->model = \app\modules\security\models\orm\Acl::class;

        $this->data = [
            [
                'module' => 'dashboard',
                'type' => '1',
                'entity_type' => 'DashboardService',
                'action' => 'getAllCollectionByFilterModel',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'crm',
                'type' => '1',
                'entity_type' => 'OrderService',
                'action' => 'getOrderList',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'crm',
                'type' => '1',
                'entity_type' => 'OrderService',
                'action' => 'getOrderCountForWeek',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'medical',
                'type' => '1',
                'entity_type' => 'AttendanceService',
                'action' => 'getAttendanceById',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'medical',
                'type' => '1',
                'entity_type' => 'AttendanceService',
                'action' => 'createAttendanceBySchedule',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'medical',
                'type' => '1',
                'entity_type' => 'AttendanceService',
                'action' => 'cancelAttendance',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'medical',
                'type' => '1',
                'entity_type' => 'AttendanceService',
                'action' => 'getAttendanceList',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'medical',
                'type' => '1',
                'entity_type' => 'EhrService',
                'action' => 'getEhrList',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'medical',
                'type' => '1',
                'entity_type' => 'PatientService',
                'action' => 'getPatientList',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'medical',
                'type' => '1',
                'entity_type' => 'PatientService',
                'action' => 'getPatientById',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'medical',
                'type' => '1',
                'entity_type' => 'ReferralService',
                'action' => 'getReferralList',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'medical',
                'type' => '1',
                'entity_type' => 'ReferralService',
                'action' => 'getReferralById',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'security',
                'type' => '1',
                'entity_type' => 'UserService',
                'action' => 'createUser',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'security',
                'type' => '1',
                'entity_type' => 'UserService',
                'action' => 'updateUser',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'security',
                'type' => '1',
                'entity_type' => 'AclService',
                'action' => 'getAclList',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'security',
                'type' => '1',
                'entity_type' => 'AclService',
                'action' => 'getAclList',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'security',
                'type' => '1',
                'entity_type' => 'AclService',
                'action' => 'add',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'security',
                'type' => '1',
                'entity_type' => 'AclService',
                'action' => 'update',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'security',
                'type' => '1',
                'entity_type' => 'AclService',
                'action' => 'deleteAcl',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'security',
                'type' => '1',
                'entity_type' => 'AclService',
                'action' => 'getAclRoleList',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'security',
                'type' => '1',
                'entity_type' => 'UserService',
                'action' => 'getUserList',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'security',
                'type' => '1',
                'entity_type' => 'UserService',
                'action' => 'getUserById',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'organization',
                'type' => '1',
                'entity_type' => 'EmployeeService',
                'action' => 'getEmployeeList',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'workplan',
                'type' => '1',
                'entity_type' => 'WorkplanService',
                'action' => 'getWorkplanList',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'config',
                'type' => '1',
                'entity_type' => 'DirectoryService',
                'action' => 'getDirectoryList',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'location',
                'type' => '1',
                'entity_type' => 'LocationService',
                'action' => 'getLocationList',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'config',
                'type' => '1',
                'entity_type' => 'WorkflowService',
                'action' => 'getWorkflowList',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'config',
                'type' => '1',
                'entity_type' => 'WorkflowStatusService',
                'action' => 'getWorkflowStatusList',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'config',
                'type' => '1',
                'entity_type' => 'WorkflowTransitionService',
                'action' => 'getWorkflowTransitionList',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
            [
                'module' => 'config',
                'type' => '1',
                'entity_type' => 'ConfigService',
                'action' => 'getAllSettings',
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id
            ],
        ];
    }
}
