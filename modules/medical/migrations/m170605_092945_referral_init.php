<?php

/**
 * Class m170605_092945_referral_init
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class m170605_092945_referral_init extends \app\common\db\Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createBothTables('{{%referral}}', [
            // todo возможно у направления еще будет специальность
            'number' => $this->string()->notNull(),
            'description' => $this->text(),
            'status' => $this->smallInteger(),
            'start_date' => $this->timestamp(),
            'end_date' => $this->timestamp(),
            'ehr_id' => $this->foreignKeyId(),
//            'employee_id' => $this->string(36),
        ]);
        $this->createResponsibilityTable('{{%referral}}');
        $this->createBothTables('{{%referral_item}}', [
            'referral_id' => $this->foreignKeyId(),
            'service_id' => $this->foreignKeyId()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropBothTables('{{%referral}}');
        $this->dropResponsibilityTable('{{%referral}}');
        $this->dropBothTables('{{%referral_item}}');
    }
}
