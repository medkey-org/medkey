<?php
use app\common\db\Migration;

/**
 * Class m180810_084449_workflow_init
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class m180810_084449_workflow_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createBothTables('{{%workflow}}', [
            'orm_module' => $this->string(),
            'orm_class' => $this->string(),
            'name' => $this->string(),
            'type' => $this->smallInteger(),
            'status' => $this->smallInteger(),
        ]);
        $this->createBothTables('{{%workflow_status}}', [
            'orm_module' => $this->string(),
            'orm_class' => $this->string(),
            'state_attribute' => $this->string(),
            'state_value' => $this->smallInteger(),
            'state_alias' => $this->string(),
            'status' => $this->smallInteger(),
            'is_start' => $this->boolean()
        ]);
        $this->createBothTables('{{workflow_transition}}', [
            'workflow_id' => $this->foreignKeyId(),
            'name' => $this->string(),
            'from_id' => $this->foreignKeyId(),
            'to_id' => $this->foreignKeyId(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropBothTables('{{%workflow}}');
        $this->dropBothTables('{{%workflow_status}}');
        $this->dropBothTables('{{%workflow_transition}}');
    }
}
