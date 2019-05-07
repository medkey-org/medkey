<?php

use app\common\db\Migration;

/**
 * Handles the creation of table `location`.
 * @package Module\Location
 * @copyright 2012-2019 Medkey
 */
class m171121_144824_create_location_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createBothTables('{{%location}}', [
            'code' => $this->smallInteger()->notNull()->unique(),
            'description' => $this->text(),
            'status' => $this->integer(),
            'start_date' => $this->timestamp(),
            'end_date' => $this->timestamp(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropBothTables('{{%location}}');
    }
}
