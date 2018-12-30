<?php

/**
 * Class m171228_073853_access_control
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class m171228_073853_access_control extends \app\common\db\Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createBothTables('{{%acl_role}}', [
            'name' => $this->string(),
            'short_name' => $this->string(),
            'description' => $this->text(),
        ]);
        $this->createBothTables('{{%acl}}', [
            'module' => $this->string(),
            'type' => $this->smallInteger(),
            'type_acl' => $this->smallInteger(),
            'entity_type' => $this->string(),
            'entity_id' => $this->foreignKeyId(),
            'acl_role_id' => $this->foreignKeyId(),
            'action' => $this->string(),
            'criteria_formula' => $this->text(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropBothTables('{{%acl_role}}');
        $this->dropBothTables('{{%acl}}');
    }
}
