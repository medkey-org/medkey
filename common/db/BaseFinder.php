<?php
namespace app\common\db;

use app\common\helpers\ClassHelper;
use yii\base\InvalidParamException;
use yii\data\ArrayDataProvider;
use yii\db\ActiveQueryInterface;
use yii\db\QueryInterface;
use app\common\base\Model;
use app\common\data\ActiveDataProvider;
use app\common\helpers\ArrayHelper;
use yii\data\DataProviderInterface;

/**
 * Class BaseFinder
 * @package Common\DB
 * @copyright 2012-2019 Medkey
 *
 * @deprecated
 */
abstract class BaseFinder extends Model
{
    /**
     * @var DataProviderInterface|array
     */
    public $provider;
    /**
     * @var string
     */
    public $createdAt;
    /**
     * @var string
     */
    public $updatedAt;
    /**
     * @var string
     */
    public $deletedAt;
    /**
     * @var QueryInterface
     */
    protected $query;
    /**
     * @var array
     */
    protected $models = [];


    /**
     * @param array $params
     * @return mixed
     */
    abstract public function search(array $params = []);

    /**
     * Инициализация состояний по умолчанию
     * @return void
     */
    protected function initCondition()
    {

    }

    /**
     * @return DataProviderInterface
     */
    protected function provider()
    {
        if ($this->provider instanceof DataProviderInterface) {
            $provider = $this->provider;
        } elseif (is_array($this->provider)) {
            $class = ArrayHelper::remove($this->provider, 'class');
            if (!ClassHelper::implementsInterface($class, 'yii\data\DataProviderInterface')) {
                throw new InvalidParamException(\Yii::t('app', 'Invalid param class provider')); // todo normalize text
            }
            /** @var DataProviderInterface $provider */
            $provider = \Yii::createObject($class, $this->provider);
        } elseif ($this->query instanceof ActiveQueryInterface) {
            $provider = new ActiveDataProvider([
                'query' => $this->query,
                'pagination' => [
                    'pageSize' => 10 // TODO CONST pageSize
                ],
            ]);
        } elseif ($this->query instanceof QueryInterface) {
            $provider = new ArrayDataProvider([
                  'allModels' => $this->query->all(),
                  'pagination' => [
                      'pageSize' => 10 // TODO CONST pageSize
                  ]
            ]);
        } elseif (is_array($this->models)) {
            $provider = new ArrayDataProvider([
                'allModels' => $this->models,
                'pagination' => [
                    'pageSize' => 10 // TODO CONST pageSize
                ]
            ]);
        } else {
            throw new InvalidParamException(\Yii::t('app', 'Invalid param provider')); // todo normalize text
        }
        $this->models = $provider->getModels();

        return $provider;
    }
}
