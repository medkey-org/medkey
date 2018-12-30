<?php
namespace app\common\dto;

use app\common\helpers\Json;
use yii\base\Arrayable;
use yii\base\UnknownPropertyException;
use yii\base\Model;

/**
 * Class Dto
 * @package Common\DTO
 * @copyright 2012-2019 Medkey
 * @deprecated
 */
class Dto
{
    /**
     * @var array
     */
    private $entity;


    /**
     * Dto constructor.
     * @param array|Model $model
     */
    public function __construct($model)
    {
        if ($model instanceof Arrayable) {
            $this->entity = $model->toArray();
        } elseif (is_array($model)) {
            $this->entity = $model;
        } elseif (is_string($model)) { // json
            $this->entity = Json::decode($model);
        }
    }

    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param Model|array $model
     * @return Dto
     */
    public static function make($model)
    {
        return new self($model);
    }

    /**
     * @todo setter наверно не нужен
     * @param string $name
     * @param string $value
     * return void
     */
    public function __set($name, $value)
    {
        if (isset($this->entity[$name]) || array_key_exists($name, $this->entity)) {
            $this->entity[$name] = $value;
        }
    }

    /**
     * @param string $name
     * @return mixed
     * @throws UnknownPropertyException
     */
    public function __get($name)
    {
        if (isset($this->entity[$name]) || array_key_exists($name, $this->entity)) {
            return $this->entity[$name];
        } else {
            throw new UnknownPropertyException('Setting unknown property: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        if (isset($this->entity[$name]) || array_key_exists($name, $this->entity)) {
            return true;
        } else {
            return false;
        }
    }
}
