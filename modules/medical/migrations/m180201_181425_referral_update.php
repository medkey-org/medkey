<?php

use app\common\db\Migration;

/**
 * Class m180201_181425_referral_update
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class m180201_181425_referral_update extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addBothColumns('{{%referral}}', 'order_id', $this->foreignKeyId());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropBothColumns('{{%referral}}', 'order_id');
    }
}
