<?php

use app\common\db\Migration;

/**
 * Class m170421_161521_phone
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */

class m170421_161521_phone extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createBothTables('{{%phone}}', [
            'entity' => $this->string(),
            'entity_id' => $this->foreignKeyId(),
            'main' => $this->smallInteger()->defaultValue(0),
            'type' => $this->string(100),
            'phone' => $this->string(255)
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropBothTables('{{%phone}}');
    }
}
