<?php

use app\common\db\Migration;

/**
 * Add dashboard table
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class m170829_102633_add_dashboard_table extends Migration
{
    public function safeUp()
    {
        $this->createBothTables('{{%dashboard}}', [
            'key' => $this->string(),
            'title' => $this->string(),
            'description' => $this->string(),
            'layout' => $this->string(),
            'type' => $this->integer(),
            'owner_id' => $this->foreignKeyId(),
        ]);
    }

    public function safeDown()
    {
        $this->dropBothTables('{{%dashboard}}');
    }
}
