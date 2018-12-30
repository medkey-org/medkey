<?php
namespace app\common\validators;

use yii\validators\NumberValidator;
use yii\validators\StringValidator;
use yii\validators\Validator;

/**
 * Class ForeignKeyValidator
 * @package Common\Validators
 * @copyright 2012-2019 Medkey
 */
class ForeignKeyValidator extends Validator
{
    /**
     * {@inheritdoc}
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        $keyType = getenv('APP_TYPE_KEY');
        if ($keyType === 'bigint') {
            if (is_int($value)) {
                $validator = new NumberValidator();
                return $validator->validateAttribute($model, $attribute);
            }
        } elseif ($keyType === 'uuid') {
            if (is_string($value)) {
                $validator = new StringValidator();
                return $validator->validateAttribute($model, $attribute);
            }
        }
    }
}
