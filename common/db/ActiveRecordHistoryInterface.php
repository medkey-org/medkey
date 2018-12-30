<?php
namespace app\common\db;

/**
 * Class ActiveRecordHistoryInterface
 * @package Common\DB
 * @copyright 2012-2019 Medkey
 */
interface ActiveRecordHistoryInterface
{
    public static function historyTableName();
    public function deleteHistory();
    public function saveHistory($attributes, $tableName);
}
