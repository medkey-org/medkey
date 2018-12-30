<?php

use app\common\db\Migration;

/**
 * Class m170421_155101_addresses
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */
class m170421_155101_address extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createBothTables('{{%address}}', [
            'entity' => $this->string(),
            'entity_id' => $this->foreignKeyId(),
            'type' => $this->string(100),       // тип юридический/фактичечкий/регистрации/проживания/иное
            'region' => $this->string(255),     // регион
            'area' => $this->string(255),       // район
            'settlement' => $this->string(255), // нас. пункт
            'street' => $this->string(255),     // улица
            'house' => $this->string(100),      // дом
            'building' => $this->string(100),   // корпус/строение/иное
            'room' => $this->string(100),       // кв/офис/иное
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropBothTables('{{%address}}');
    }
}
