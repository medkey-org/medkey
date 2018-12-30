<?php

/**
 * Class m170604_132416_service_init
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class m170604_132416_service_init extends \app\common\db\Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createBothTables('{{%service}}', [
            'code' => $this->string()->notNull(),
            'title' => $this->text()->notNull(),
            'program' => $this->smallInteger(), // OMS/DMS/PAID
            'short_title' => $this->string(),
            'description' => $this->text(),
            'speciality_id' => $this->foreignKeyId(),
            'status' => $this->smallInteger()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropBothTables('{{%service}}');
    }
}
