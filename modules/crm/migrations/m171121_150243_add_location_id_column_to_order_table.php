<?php

use app\common\db\Migration;

/**
 * Handles adding location_id to table `order`.
 * @package Module\CRM
 * @copyright 2012-2019 Medkey
 */
class m171121_150243_add_location_id_column_to_order_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addBothColumns('{{%order}}', 'location_id', $this->foreignKeyId());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropBothColumns('{{%order}}', 'location_id');
    }
}
