<?php

use app\common\db\Migration;

/**
 * Class m181221_162828_speciality
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */
class m181221_162828_speciality extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createBothTables('{{%speciality}}', [
            'title' => $this->string(),
            'short_title' => $this->string(),
            'description' => $this->text()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropBothTables('{{%speciality}}');
    }
}
