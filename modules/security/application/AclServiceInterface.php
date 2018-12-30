<?php
namespace app\modules\security\application;

use app\common\base\Model;
use app\common\db\ActiveRecord;

/**
 * Class AclServiceInterface
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
interface AclServiceInterface
{
    public function getAclList(Model $form);
    public function getAclRoleList(Model $form);
    public function add($aclDto, $scenario = ActiveRecord::SCENARIO_CREATE);
    public function update($id, $aclDto, $scenario = ActiveRecord::SCENARIO_UPDATE);
}
