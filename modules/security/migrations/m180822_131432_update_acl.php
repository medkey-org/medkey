<?php

use app\common\db\Migration;

/**
 * Class m180822_131432_update_acl
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class m180822_131432_update_acl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropBothColumns('{{%acl}}', 'type_acl');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addBothColumns('{{%acl}}', 'type_acl', $this->smallInteger());
    }
}
