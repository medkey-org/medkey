<?php

use app\common\db\Migration;

/**
 * Class m190601_151020_document_template
 */
class m190601_151020_document_template extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createBothTables('{{%document_template}}', [
            'title' => $this->string(),
            'content' => $this->text(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropBothTables('{{%document_template}}');
    }
}
