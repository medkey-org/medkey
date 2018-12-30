<?php
namespace app\modules\config\models\orm;

use app\common\db\ActiveRecord;

/**
 * Class WorkflowEntity
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class WorkflowEntity extends ActiveRecord
{
    /**
     * @return mixed
     */
    public function getWorkflow()
    {
        return $this->hasOne(Workflow::class, ['id' => 'workflow_id']);
    }
}
