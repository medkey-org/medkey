<?php

use app\common\db\Migration;

/**
 * Class m170517_125330_order_init
 * @package Module\CRM
 * @copyright 2012-2019 Medkey
 */
class m170517_125330_order_init extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // todo возможно в заказе должен быть тип баллов тоже. Сейчас есть только у айтемов.
        $this->createBothTables('{{%order}}', [
            'number' => $this->string(),
            'status' => $this->smallInteger(),
            'ehr_id' => $this->foreignKeyId(),
            'currency' => $this->string(),
            'currency_sum' => $this->integer(),
            'final_currency_sum' => $this->integer(),
            'type' => $this->smallInteger()
        ]);
        $this->createBothTables('{{%order_item}}', [
            'order_id' => $this->foreignKeyId(),
            'item_number' => $this->smallInteger(), // номер позиции чека
            'currency' => $this->string(),
            'currency_sum_per_unit' => $this->integer(),
            'currency_sum' => $this->integer(),
            'final_currency_sum' => $this->integer(),
            'discount_point' => $this->integer(),
            'discount_currency_sum' => $this->integer(),
            'service_id' => $this->foreignKeyId(),
            'qty' => $this->smallInteger(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropBothTables('{{%order}}');
        $this->dropBothTables('{{%order_item}}');
    }
}
