<?php

use app\common\db\Migration;

/**
 * Class m180806_122255_user_status
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class m180806_122255_user_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addBothColumns('{{%user}}', 'status', $this->smallInteger());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropBothColumns('{{%user}}', 'status');
    }
}
