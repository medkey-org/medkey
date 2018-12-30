<?php

/**
 * Class m181105_135102_user_access_token
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class m181105_135102_user_access_token extends \app\common\db\Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addBothColumns('{{%user}}', 'access_token', $this->string());
        $this->addBothColumns('{{%user}}', 'expire_token_ts', $this->timestamp());
        $this->createIndex('unique_access_token', '{{%user}}', 'access_token', true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropBothColumns('{{%user}}', 'access_token');
        $this->dropBothColumns('{{%user}}', 'expire_token_ts');
    }
}
