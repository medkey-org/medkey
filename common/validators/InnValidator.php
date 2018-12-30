<?php
namespace app\common\validators;

use yii\validators\Validator;

/**
 * Russian INN Validator
 *
 * INN validator (10-digit for legals, 12-digits for person)
 * in rules()   [['inn'], 'app\components\validators\validateInn'],
 * for test purpose use fake INN number 1234567894 or 123456789110
 *
 * @package Common\Validators
 * @copyright 2012-2019 Medkey
 */
class InnValidator extends Validator
{
    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $result = false;
        $inn = (string)preg_replace('/[^0-9]/', '', $model->$attribute);
        $len = strlen($inn);

        if (!$inn) {
            $this->addError($model, $attribute, \Yii::t('app', 'INN cannot be empty'));
        } elseif (preg_match('/[^0-9]/', $inn)) {
            $this->addError($model, $attribute, \Yii::t('app', 'INN can be only numeric'));
        } elseif (!in_array($len, [10, 12])) {
            $this->addError($model, $attribute, \Yii::t('app', 'Incorrect INN length'));
        } else {
            switch ($len) {
                case 10:
                    $n10 = $this->checkDigits($inn, [2, 4, 10, 3, 5, 9, 4, 6, 8]);

                    if ($n10 === (int)$inn[9]) {
                        $result = true;
                    }
                    break;
                case 12:
                    $n11 = $this->checkDigits($inn, [7, 2, 4, 10, 3, 5, 9, 4, 6, 8]);
                    $n12 = $this->checkDigits($inn, [3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8]);
                    if (($n11 === $inn[10]) && ($n12 === $inn[11])) {
                        $result = true;
                    }
                    break;
            }

            if (!$result) {
                $this->addError($model, $attribute, \Yii::t('app', 'Incorrect INN checksum'));
            }
        }
    }

    /**
     * Validate digits checksum of INN
     * @param $inn
     * @param Array $coefficients
     * @return int
     */
    protected function checkDigits($inn, $coefficients)
    {
        $n = 0;
        foreach ($coefficients as $i => $k) {
            $n += $k * (int)$inn{$i};
        }
        return ($n % 11) % 10;
    }
}
