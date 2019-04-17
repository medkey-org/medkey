<?php
namespace app\common\logic\orm;

/**
 * Trait HumanTrait
 *
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 * @property int $gender
 *
 * @package Common\Logic
 * @copyright 2012-2019 Medkey
 */
trait HumanTrait
{
    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middle_name;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->last_name . ' ' . $this->first_name . ' ' . $this->middle_name;
    }

    /**
     * @todo in form
     * @return array
     */
    public static function sexListData()
    {
        return [
            static::SEX_MALE => \Yii::t('app', 'Male'),
            static::SEX_FEMALE => \Yii::t('app','Female'),
        ];
    }

    /**
     * @return string
     */
    public function getSexName()
    {
        $sex = $this::sexListData();

        return !empty($sex[$this->sex]) ? $sex[$this->sex] : '';
    }
}
