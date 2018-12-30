<?php
namespace app\common\logic\orm;

use app\common\db\ActiveRecord;

/**
 * Class Address
 * @package app\common\logic\orm
 * @copyright 2012-2019 Medkey
 */
class Address  extends ActiveRecord
{
    const TYPE_FACT = 1;
    const TYPE_FACT_NAME = 'Actual'; // todo normalize text
    const TYPE_REG = 2;
    const TYPE_REG_NAME = 'Registration'; // todo normalize text


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['type'], 'integer' ],
            [ ['region'], 'string'],
            [ ['region_code'], 'string'],
            [ ['area'], 'string'],
            [ ['area_code'], 'string'],
            [ ['settlement'], 'string'],
            [ ['settlement_code'], 'string'],
            [ ['city'], 'string'],
            [ ['city_code'], 'string'],
            [ ['additional_area'], 'string'],
            [ ['additional_area_code'], 'string'],
            [ ['additional_area_streets'], 'string'],
            [ ['additional_area_streets_code'], 'string'],
            [ ['in_city_area'], 'string'],
            [ ['in_city_area_code'], 'string'],
            [ ['street'], 'string'],
            [ ['street_code'], 'string'],
            [ ['house'], 'string'],
            [ ['house_code'], 'string'],
            [ ['building'], 'string'],
            [ ['building_code'], 'string'],
            [ ['room'], 'string']
        ];
    }

    /**
     * @return array
     */
    public static function typeListData()
    {
        return [
            self::TYPE_FACT => self::TYPE_FACT_NAME,
            self::TYPE_REG => self::TYPE_REG_NAME
        ];
    }

    public function getFullAddress()
    {
        return $this->region . ' ' . $this->area . ' ' . $this->city . ' ' . $this->settlement . ' ' . $this->street . ' ' . $this->house . ' ' . $this->building . ' ' . $this->room;
    }

    /**
     * @return array
     */
    public function attributeLabelsOverride()
    {
        return [
            'type' => \Yii::t('app', 'Type address'),
            'region' => \Yii::t('app', 'Region'),
            'area' => \Yii::t('app', 'Area'),
            'city' => \Yii::t('app', 'City'),
            'settlement' => \Yii::t('app', 'Settlement'),
            'street' => \Yii::t('app', 'Street'),
            'house' => \Yii::t('app', 'House'),
            'building' => \Yii::t('app', 'Building'),
            'room' => \Yii::t('app', 'Room'),
        ];
    }
}
