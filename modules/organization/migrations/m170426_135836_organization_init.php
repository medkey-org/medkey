<?php

use app\common\db\Migration;

/**
 * Class m170426_135836_organization_init
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */
class m170426_135836_organization_init extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createBothTables('{{%organization}}', [
            'title' => $this->string(),
            'short_title' => $this->string(),
            'description' => $this->text()
        ]);
        $this->createBothTables('{{%department}}', [
            'organization_id' => $this->foreignKeyId(),
            'title' => $this->string(),
            'short_title' => $this->string(),
            'description' => $this->text()
        ]);
        $this->createBothTables('{{%position}}', [
            'department_id' => $this->foreignKeyId(),
            'title' => $this->string(),
            'short_title' => $this->string(),
            'description' => $this->text()
        ]);
        $this->createBothTables('{{%employee}}', [
            'user_id' => $this->foreignKeyId(),
            'first_name' => $this->string(),
            'middle_name' => $this->string(),
            'last_name' => $this->string(),
            'status' => $this->smallInteger(),
            'sex' => $this->smallInteger(),
            'education' => $this->string(),
            'birthday' => $this->date()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropBothTables('{{%employee}}');
        $this->dropBothTables('{{%organization}}');
        $this->dropBothTables('{{%department}}');
        $this->dropBothTables('{{%position}}');
    }
}
