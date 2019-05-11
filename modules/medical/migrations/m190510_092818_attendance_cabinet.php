<?php

use app\common\db\Migration;

/**
 * Class m190510_092818_attendance_cabinet
 */
class m190510_092818_attendance_cabinet extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addBothColumns('{{%attendance}}', 'cabinet_id', $this->foreignKeyId());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropBothColumns('{{%attendance}}', 'cabinet_id');
    }
}
