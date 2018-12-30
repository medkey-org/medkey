<?php

use app\common\db\Migration;

/**
 * Class m180115_135652_seed_table
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class m180115_135652_seed_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createBothTables('{{%seed}}', [
            'class_name' => $this->string(),
        ]);

        $this->createBothTables('{{%seed_record}}', [
            'seed_id' => $this->foreignKeyId(),
            'model' => $this->string(),
            'pk' => $this->text(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropBothTables('{{%seed}}');
        $this->dropBothTables('{{%seed_record}}');
    }
}
