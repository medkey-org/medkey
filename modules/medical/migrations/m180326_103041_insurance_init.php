<?php

use app\common\db\Migration;

/**
 * Class m180326_103041_insurance_init
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class m180326_103041_insurance_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createBothTables('{{%insurance}}', [
            'code' => $this->integer(),
            'title' => $this->text(),
            'short_title' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropBothTables('{{%insurance}}');
    }
}
