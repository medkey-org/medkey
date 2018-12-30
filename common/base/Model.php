<?php
namespace app\common\base;

use yii\base\InvalidValueException;
use yii\db\ActiveRecordInterface;

/**
 * Class Model
 * @package Common\Base
 * @copyright 2012-2019 Medkey
*/
class Model extends \yii\base\Model implements PrepareModelInterface
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_DELETE = 'delete';

    /**
     * @param mixed $model
     * @param string $scenario
     * @param array $data
     * @return mixed|Model
     */
    public static function ensure($model, $scenario = self::SCENARIO_DEFAULT, $data = [])
    {
        if (is_array($model)) {
            $m = new static([
                'scenario' => $scenario,
            ]);
            $m->load($data);
            return $m;
        } elseif ($model instanceof \yii\base\Model) {
            $m = clone $model;
            $m->setScenario($scenario);
            $m->load($data);
            return $m;
        } else {
            $m = new static([
                'scenario' => $scenario,
            ]);
            $m->load($data);
            return $m;
        }
    }

    /**
     * @param ActiveRecordInterface $ar
     * @param bool $safeOnly
     * @return bool
     */
    public function loadAr($ar, bool $safeOnly = true) : bool
    {
        if (!($ar instanceof ActiveRecordInterface)) {
            throw new InvalidValueException('Param is not instance of ' . Model::class . ' class');
        }
        $attributes = array_flip($safeOnly ? $this->safeAttributes() : $this->attributes());
        foreach ($attributes as $attribute => $value) {
            if (isset($ar->{$attribute})) {
                $this->{$attribute} =  $ar->{$attribute};
            }
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = [self::SCENARIO_DEFAULT => [], self::SCENARIO_CREATE => [], self::SCENARIO_UPDATE => [], self::SCENARIO_DELETE => []];
        foreach ($this->getValidators() as $validator) {
            foreach ($validator->on as $scenario) {
                $scenarios[$scenario] = [];
            }
            foreach ($validator->except as $scenario) {
                $scenarios[$scenario] = [];
            }
        }
        $names = array_keys($scenarios);

        foreach ($this->getValidators() as $validator) {
            if (empty($validator->on) && empty($validator->except)) {
                foreach ($names as $name) {
                    foreach ($validator->attributes as $attribute) {
                        $scenarios[$name][$attribute] = true;
                    }
                }
            } elseif (empty($validator->on)) {
                foreach ($names as $name) {
                    if (!in_array($name, $validator->except, true)) {
                        foreach ($validator->attributes as $attribute) {
                            $scenarios[$name][$attribute] = true;
                        }
                    }
                }
            } else {
                foreach ($validator->on as $name) {
                    foreach ($validator->attributes as $attribute) {
                        $scenarios[$name][$attribute] = true;
                    }
                }
            }
        }

        foreach ($scenarios as $scenario => $attributes) {
            if (!empty($attributes)) {
                $scenarios[$scenario] = array_keys($attributes);
            }
        }

        return $scenarios;
    }
}
