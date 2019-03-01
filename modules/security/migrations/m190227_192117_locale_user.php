<?php

use app\common\db\Migration;
use app\modules\security\models\orm\User;
/**
 * Class m190227_192117_locale_user
 */
class m190227_192117_locale_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addBothColumns('{{%user}}', 'language', $this->string()->defaultValue(User::LANG_EN));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropBothColumns('{{%user}}', 'language');
    }
}
