<?php

use app\common\db\Migration;

/**
 * Class m181016_144250_add_column_skype_bot_id
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */
class m181016_144250_add_column_skype_bot_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addBothColumns('{{%employee}}', 'skype_bot_id', $this->string());
        $this->addBothColumns('{{%employee}}', 'skype_code', $this->string(6));
        $this->addBothColumns('{{%employee}}', 'skype_service_url', $this->string());
        $this->addBothColumns('{{%notification}}', 'skype_service_url', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropBothColumns('{{%employee}}', 'skype_bot_id');
        $this->dropBothColumns('{{%employee}}', 'skype_code');
        $this->dropBothColumns('{{%employee}}', 'skype_service_url');
        $this->dropBothColumns('{{%notification}}', 'skype_service_url');
    }
}
