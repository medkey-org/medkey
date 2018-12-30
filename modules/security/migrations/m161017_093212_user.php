<?php

use app\common\db\Migration;

/**
 * Class m161017_093212_security_init
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class m161017_093212_user extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createBothTables('{{%user}}', [
            'login' => $this->string()->notNull(),
            'password_hash' => $this->string(255)->notNull(),
            'acl_role_id' => $this->foreignKeyId(),
            'auth_key' => $this->string(),
            'password_reset_token' => $this->string()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropBothTables('{{%user}}');
    }
}
