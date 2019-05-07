<?php

use app\common\db\Migration;

/**
 * Class m190507_144925_lang_options
 */
class m190507_144925_lang_options extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropBothColumns('{{%user}}', 'language');
        $this->addBothColumns('{{%user}}', 'language', $this->string());
        $this->execute("INSERT INTO {{%config}} (key, value) VALUES ('language', 'en-US')");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }
}
