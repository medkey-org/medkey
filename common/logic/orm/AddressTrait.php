<?php
namespace app\common\logic\orm;

use app\common\db\ActiveRecord;

/**
 * Trait AddressTrait
 * @mixin ActiveRecord
 * @package Common\Logic
 * @copyright 2012-2019 Medkey
 */
trait AddressTrait
{
    public function getAddresses()
    {
        return $this->hasMany(Address::class, ['entity_id' => 'id'])
            ->andWhere([
                'entity' => static::getTableSchema()->name,
            ]);
    }
}
