<?php
namespace app\common\logic\orm;

use app\common\db\ActiveRecord;

/**
 * Class PassportTrait
 * @mixin ActiveRecord
 * @package Common\Logic
 * @copyright 2012-2019 Medkey
 */
trait PassportTrait
{
    /**
     * @return mixed
     */
    public function getPassport()
    {
        return $this->hasOne(Passport::class, ['entity_id' => 'id'])
            ->andOnCondition(['entity' => static::getTableSchema()->name]);
    }
}
