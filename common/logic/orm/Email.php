<?php
namespace app\common\logic\orm;

use app\common\db\ActiveRecord;
use yii\validators\EmailValidator;
use yii\validators\RequiredValidator;

/**
 * Class Email
 *
 * @property string $id
 * @property int $type
 * @property string $address
 * @property string $entity
 * @property string $entity_id
 *
 * @package Common\Logic
 * @copyright 2012-2019 Medkey
 */
class Email extends ActiveRecord
{
    const TYPE_HOME = 1;
    const TYPE_HOME_NAME = 'Личный'; // todo normalize text
    const TYPE_WORK = 3;
    const TYPE_WORK_NAME = 'Раб.'; // todo normalize text


    /**
     * @param array $attribute
     * @return null|void
     */
    public function validateEmails($attribute)
    {
        $emailValidator = new EmailValidator();

        if (!is_array($this->{$attribute})) {
            return null;
        }
        foreach ($this->{$attribute} as $index => $row) {
            $error = null;
            if (empty($row['address'])) {
                continue;
            }
            $emailValidator->validate($row['address'], $error);
            if (!empty($error)) {
                $key = $attribute . '[' . $index . '][address]';
                $this->addError($key, $error);
            }
        }

        $requireValidator = new RequiredValidator();

        if (!is_array($this->{$attribute})) {
            return null;
        }
        foreach ($this->{$attribute} as $index => $row) {
            if ($index === 0) { // not required first row
                continue;
            }
            $error = null;
            $requireValidator->validate($row['type'], $error);
            if (!empty($error)) {
                $key = $attribute . '[' . $index . '][type]';
                $this->addError($key, $error);
            }
            $requireValidator->validate($row['address'], $error);
            if (!empty($error)) {
                $key = $attribute . '[' . $index . '][address]';
                $this->addError($key, $error);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['type', 'address'], 'required' ],
            [ ['type'], 'integer' ],
            [ ['address'], 'string' ]
        ];
    }

    /**
     * @return array
     */
    public static function typeListData()
    {
        return [
            self::TYPE_HOME => self::TYPE_HOME_NAME,
            self::TYPE_WORK => self::TYPE_WORK_NAME
        ];
    }

    /**
     * Get gender name
     *
     * @return string
     */
    public function getTypeName()
    {
        $types  = $this::typeListData();

        return !empty($types[$this->type]) ? $types[$this->type] : '';
    }

    /**
     * @return array
     */
    public function attributeLabelsOverride()
    {
        return [
        ];
    }
}
