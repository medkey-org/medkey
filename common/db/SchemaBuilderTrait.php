<?php
namespace app\common\db;

/**
 * Extend Yii2 \yii\db\SchemaBuilderTrait functions
 * @mixin Migration
 * @copyright 2012-2019 Medkey
 */
trait SchemaBuilderTrait
{
    /**
     * Return application's foreign key type
     * @param null $type
     * @return mixed
     */
    public function foreignKeyId($type = null)
    {
        if (!is_null($type) && method_exists($this, $type)) {
            return $this->{$type}();
        }
        if ($this->pkType === 'bigint') {
            return $this->bigInteger();
        } elseif ($this->pkType === 'uuid') {
            return $this->string(36);
        }
    }
}
