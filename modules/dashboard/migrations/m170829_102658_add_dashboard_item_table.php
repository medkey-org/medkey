<?php

use app\common\db\Migration;

/**
 * Add dashboard item table
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class m170829_102658_add_dashboard_item_table extends Migration
{
    public function safeUp()
    {
        $this->createBothTables('{{%dashboard_item}}', [
            'title' => $this->string(),
            'dashboard_id' => $this->foreignKeyId(),
            'widget' => $this->string(),
            'position' => $this->integer(),
            'order' => $this->integer(),
        ]);
    }

    public function safeDown()
    {
        $this->dropBothTables('{{%dashboard_item}}');
    }
}
