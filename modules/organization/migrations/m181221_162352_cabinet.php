<?php

use app\common\db\Migration;

/**
 * Class m181221_162352_cabinet
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */
class m181221_162352_cabinet extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createBothTables('{{%cabinet}}', [
            'number' => $this->string(),
            'description' => $this->text(),
            'organization_id' => $this->foreignKeyId(),
            'department_id' => $this->foreignKeyId(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropBothTables('{{%cabinet}}');
    }
}
