<?php

use app\common\db\Migration;

/**
 * Class m170614_073209_add_address_fias_fiels
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */
class m170614_073209_add_address_fias_fiels extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addBothColumns('{{%address}}', 'city', $this->string());
        $this->addBothColumns('{{%address}}', 'city_code', $this->string(30));
        $this->addBothColumns('{{%address}}', 'additional_area', $this->string());
        $this->addBothColumns('{{%address}}', 'additional_area_code', $this->string(30));
        $this->addBothColumns('{{%address}}', 'additional_area_streets', $this->string());
        $this->addBothColumns('{{%address}}', 'additional_area_streets_code', $this->string(30));
        $this->addBothColumns('{{%address}}', 'in_city_area', $this->string());
        $this->addBothColumns('{{%address}}', 'in_city_area_code', $this->string());
        $this->addBothColumns('{{%address}}', 'region_code', $this->string(30));
        $this->addBothColumns('{{%address}}', 'area_code', $this->string(30));
        $this->addBothColumns('{{%address}}', 'settlement_code', $this->string(30));
        $this->addBothColumns('{{%address}}', 'street_code', $this->string(30));
        $this->addBothColumns('{{%address}}', 'house_code', $this->string(30));
        $this->addBothColumns('{{%address}}', 'building_code', $this->string(30));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropBothColumns('{{%address}}', 'city');
        $this->dropBothColumns('{{%address}}', 'city_code');
        $this->dropBothColumns('{{%address}}', 'additional_area');
        $this->dropBothColumns('{{%address}}', 'additional_area_code');
        $this->dropBothColumns('{{%address}}', 'additional_area_streets');
        $this->dropBothColumns('{{%address}}', 'additional_area_streets_code');
        $this->dropBothColumns('{{%address}}', 'in_city_area');
        $this->dropBothColumns('{{%address}}', 'in_city_area_code');
        $this->dropBothColumns('{{%address}}', 'region_code');
        $this->dropBothColumns('{{%address}}', 'area_code');
        $this->dropBothColumns('{{%address}}', 'settlement_code');
        $this->dropBothColumns('{{%address}}', 'street_code');
        $this->dropBothColumns('{{%address}}', 'house_code');
        $this->dropBothColumns('{{%address}}', 'building_code');
    }
}
