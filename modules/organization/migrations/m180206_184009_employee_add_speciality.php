<?php

use app\common\db\Migration;

/**
 * Class m180206_184009_employee_add_speciality
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */
class m180206_184009_employee_add_speciality extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addBothColumns('{{%employee}}', 'speciality_id', $this->foreignKeyId());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropBothColumns('{{%employee}}', 'speciality_id');
    }
}
