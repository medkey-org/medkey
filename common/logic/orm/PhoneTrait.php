<?php
namespace app\common\logic\orm;

use app\common\db\ActiveRecord;

/**
 * Trait PhoneTrait
 * @mixin ActiveRecord
 * @package Common\Logic
 * @copyright 2012-2019 Medkey
 */
trait PhoneTrait
{
    public function getPhones()
    {
        return $this->hasMany(Phone::class, ['entity_id' => 'id'])
            ->andOnCondition(['entity' => static::getTableSchema()->name]);
    }
}
