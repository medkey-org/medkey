<?php

use app\common\db\Migration;

/**
 * Class m180324_175013_document_init
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */
class m180324_175013_document_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createBothTables('{{%passport}}', [
            'series' => $this->integer(),
            'number' => $this->integer(),
            'issue_date' => $this->date(),
            'subdivision_code' => $this->integer(),
            'subdivision_name' => $this->integer(),
            'entity' => $this->string(),
            'entity_id' => $this->foreignKeyId(),
        ]);
        $this->createBothTables('{{%policy}}', [
            'expiration_date' => $this->date(),
            'issue_date' => $this->date(),
            'insurance_id' => $this->foreignKeyId(),
            'number' => $this->integer(),
            'series' => $this->integer(),
            'type' => $this->smallInteger(),
            'patient_id' => $this->foreignKeyId(),
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropBothTables('{{%passport}}');
        $this->dropBothTables('{{%policy}}');
    }
}
