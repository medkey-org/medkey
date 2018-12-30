<?php

use app\common\db\Migration;

/**
 * Class m180204_153849_attendance_update
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class m180204_153849_attendance_update extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addBothColumns('{{%attendance}}', 'employee_id', $this->foreignKeyId());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropBothColumns('{{%attendance}}', 'employee_id');
    }
}
