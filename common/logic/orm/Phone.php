<?php
namespace app\common\logic\orm;

use app\common\db\ActiveRecord;

/**
 * Class Phone
 *
 * @property string $id
 * @property int $type
 * @property string $phone
 * @property string $entity_id
 * @property string $entity
 *
 * @package Common\Logic
 * @copyright 2012-2019 Medkey
 */
class Phone extends ActiveRecord
{
    const TYPE_MOBILE = 1;
    const TYPE_MOBILE_NAME = 'Mobile.';
    const TYPE_HOME = 2;
    const TYPE_HOME_NAME = 'Personal';
    const TYPE_WORK = 3;
    const TYPE_WORK_NAME = 'Work';


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['type', 'phone'], 'required' ],
            [ ['type'], 'integer' ],
            [ ['phone'], 'string' ]
        ];
    }

    /**
     * @return array
     */
    public static function typeListData()
    {
        return [
            self::TYPE_MOBILE => self::TYPE_MOBILE_NAME,
            self::TYPE_HOME => self::TYPE_HOME_NAME,
            self::TYPE_WORK => self::TYPE_WORK_NAME
        ];
    }

    /**
     * @return string
     */
    public function getTypeName()
    {
        $types  = $this::typeListData();

        return !empty($types[$this->type]) ? $types[$this->type] : '';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabelsOverride()
    {
        return [
        ];
    }
}
