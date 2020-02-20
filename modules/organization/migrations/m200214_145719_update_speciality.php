<?php

use app\common\db\Migration;


class m200214_145719_update_speciality extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createBothTables('{{%employee_to_speciality}}', [
            'employee_id' => $this->foreignKeyId(),
            'speciality_id' => $this->foreignKeyId(),
        ]);
        $this->dropBothColumns('{{%employee}}', 'speciality_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropBothTables('{{%employee_to_speciality}}');
        $this->addBothColumns('{{%employee}}', 'speciality_id', $this->foreignKeyId());
    }
}
