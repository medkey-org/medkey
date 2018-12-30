<?php

use app\common\db\Migration;

/**
 * Handles adding description to table `order`.
 * @package Module\CRM
 * @copyright 2012-2019 Medkey
 */
class m171123_135857_add_description_column_to_order_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addBothColumns('{{%order}}', 'description', $this->text());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropBothColumns('{{%order}}', 'description');
    }
}
