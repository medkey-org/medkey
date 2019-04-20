<?php
namespace app\modules\workplan\models\orm;

use app\common\db\ActiveRecord;

/**
 * Class WorkplanToWeek
 *
 * @package Module\Workplan
 * @copyright 2012-2019 Medkey
 */
class WorkplanToWeek extends ActiveRecord
{
    public static function modelIdentity()
    {
        return ['workplan_id', 'week'];
    }
}
