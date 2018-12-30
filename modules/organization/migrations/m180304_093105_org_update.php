<?php

use app\common\db\Migration;

/**
 * Class m180304_093105_org_update
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */
class m180304_093105_org_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addBothColumns('{{%employee}}', 'department_id', $this->foreignKeyId());
        $this->addBothColumns('{{%employee}}', 'position_id', $this->foreignKeyId()); // пока одна
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropBothColumns('{{%employee}}', 'department_id');
        $this->dropBothColumns('{{%employee}}', 'position_id');
    }
}
