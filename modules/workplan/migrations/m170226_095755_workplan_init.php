<?php

/**
 * Class m170226_095755_workplan_init
 * @package Module\Workplan
 * @copyright 2012-2019 Medkey
 */
class m170226_095755_workplan_init extends \app\common\db\Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createBothTables('{{%workplan}}', [
            'since_date' => $this->date(),
            'expire_date' => $this->date(),
            'since_time' => $this->time(),
            'expire_time' => $this->time(),
            'rules' => $this->text(),
            'employee_id' => $this->foreignKeyId(),
            'cabinet_id' => $this->foreignKeyId(),
            'department_id' => $this->foreignKeyId(),
            'status' => $this->smallInteger()
        ]);

        $this->createBothTables('{{%workplan_to_week}}', [
            'workplan_id' => $this->foreignKeyId(),
            'week' => $this->smallInteger(),
            'rule' => $this->string()
        ]);

        $this->createBothTables('{{%exclusion}}', [
            'name' => $this->bigInteger(),
            'type' => $this->smallInteger()
        ]);

        $this->createBothTables('{{%workplan_to_exclusion}}', [
            'workplan_id' => $this->foreignKeyId(),
            'exclusion_id' => $this->foreignKeyId()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropBothTables('{{%workplan}}');
        $this->dropBothTables('{{%workplan_to_week}}');
        $this->dropBothTables('{{%exclusion}}');
        $this->dropBothTables('{{%workplan_to_exclusion}}');
    }
}
