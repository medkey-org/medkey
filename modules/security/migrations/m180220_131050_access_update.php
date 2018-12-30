<?php

use app\common\db\Migration;

/**
 * Class m180220_131050_access_update
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class m180220_131050_access_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addBothColumns('{{%acl}}', 'rule', $this->smallInteger());
        $this->addBothColumns('{{%acl}}', 'parent', $this->foreignKeyId());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropBothColumns('{{%acl}}', 'rule');
        $this->dropBothColumns('{{%acl}}', 'parent');
    }
}
