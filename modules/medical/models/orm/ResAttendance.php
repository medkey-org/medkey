<?php
namespace app\modules\medical\models\orm;

use app\common\db\ActiveRecord;

/**
 * Class ResAttendance
 *
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ResAttendance extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%res__attendance}}';
    }
}
