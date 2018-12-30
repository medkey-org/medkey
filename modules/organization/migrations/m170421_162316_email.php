<?php

use app\common\db\Migration;

/**
 * Class m170421_162316_email_address
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */

class m170421_162316_email extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createBothTables('{{%email}}', [
            'entity' => $this->string(),
            'entity_id' => $this->foreignKeyId(), // todo length uuid
            'type' => $this->string(100),
            'address' => $this->string(255)
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropBothTables('{{%email}}');
    }
}
