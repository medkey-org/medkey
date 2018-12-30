<?php

/**
 * Class m170605_114641_ambulatory_init
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class m170605_114641_attendance_init extends \app\common\db\Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createBothTables('{{%attendance}}', [
            'ehr_id' => $this->foreignKeyId(),
//            'employee_id' => $this->string(36),
            'workplan_id' => $this->foreignKeyId(),
            'status' => $this->smallInteger(),
            'datetime' => $this->timestamp(),
            'type' => $this->smallInteger()
        ]);
        $this->createBothTables('{{referral_to_attendance}}', [
            'referral_id' => $this->foreignKeyId(),
            'attendance_id' => $this->foreignKeyId(),
        ]);
        $this->createResponsibilityTable('{{%attendance}}');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropBothTables('{{%attendance}}');
        $this->dropBothTables('{{%referral_to_attendance}}');
        $this->dropResponsibilityTable('{{%attendance}}');
    }
}
