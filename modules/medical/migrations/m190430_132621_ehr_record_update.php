<?php

use app\common\db\Migration;

/**
 * Class m190430_132621_ehr_record_update
 */
class m190430_132621_ehr_record_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addBothColumns('{{%ehr_record}}', 'name', $this->text());
        $this->addBothColumns('{{%ehr_record}}', 'complaints', $this->text());
        $this->addBothColumns('{{%ehr_record}}', 'diagnosis', $this->text());
        $this->addBothColumns('{{%ehr_record}}', 'recommendations', $this->text());
        $this->addBothColumns('{{%ehr_record}}', 'preliminary', $this->boolean());
        $this->addBothColumns('{{%ehr_record}}', 'revisit', $this->timestamp());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropBothColumns('{{%ehr_record}}', 'name');
        $this->dropBothColumns('{{%ehr_record}}', 'complaints');
        $this->dropBothColumns('{{%ehr_record}}', 'diagnosis');
        $this->dropBothColumns('{{%ehr_record}}', 'recommendations');
        $this->dropBothColumns('{{%ehr_record}}', 'preliminary');
        $this->dropBothColumns('{{%ehr_record}}', 'revisit');
    }
}
