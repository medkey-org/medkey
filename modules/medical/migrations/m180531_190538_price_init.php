<?php

use app\common\db\Migration;

/**
 * Class m180531_190538_price_init
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class m180531_190538_price_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createBothTables('{{%service_price}}', [
            'cost' => $this->integer(),
            'service_id' => $this->foreignKeyId(),
            'service_price_list_id' => $this->foreignKeyId(),
            'status' => $this->integer(),
        ]);
        $this->createBothTables('{{%service_price_list}}', [
            'name' => $this->string(),
            'status' => $this->smallInteger(),
            'currency' => $this->string(),
            'start_date' => $this->timestamp(),
            'end_date' => $this->timestamp()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropBothTables('{{%service_price}}');
        $this->dropBothTables('{{%service_price_list}}');
    }
}
