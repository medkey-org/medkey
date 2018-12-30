<?php

use app\common\db\Migration;

/**
 * Handles the creation of table `notification`.
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class m181002_113601_create_notification_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createBothTables('{{%notification}}', [
            'type' => $this->smallInteger(),
            'status' => $this->smallInteger(),
            'to' => $this->string(),
            'message' => $this->text(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropBothTables('{{%notification}}');
    }
}
