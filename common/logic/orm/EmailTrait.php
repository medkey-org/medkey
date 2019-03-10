<?php
namespace app\common\logic\orm;

use app\common\db\ActiveRecord;
use app\common\logic\orm\Email;

/**
 * Trait EmailTrait
 * @mixin ActiveRecord
 * @package Common\Logic
 * @copyright 2012-2019 Medkey
 */
trait EmailTrait
{
    public function getEmails()
    {
        return $this->hasMany(Email::class, ['entity_id' => 'id'])
            ->andWhere([
                'entity' => static::getTableSchema()->name,
            ]);
    }
}
