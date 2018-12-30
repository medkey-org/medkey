<?php

use app\common\db\Migration;

/**
 * Class m180127_150144_attendance_update
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class m180127_150144_attendance_update extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addBothColumns('{{%attendance}}', 'number', $this->integer());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropBothColumns('{{%attendance}}', 'number');
    }
}
