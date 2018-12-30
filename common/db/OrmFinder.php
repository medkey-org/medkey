<?php
namespace app\common\db;

use app\common\helpers\ClassHelper;
use yii\base\InvalidCallException;
use yii\db\ActiveRecordInterface;
use yii\base\InvalidValueException;

/**
 * Class OrmFinder
 * @package Common\DB
 * @copyright 2012-2019 Medkey
 * @deprecated
 */
class OrmFinder extends BaseFinder
{
    /**
     * @var string OrmClass
     */
    public $modelClass;


    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!isset($this->modelClass)) {
            $module = \Yii::$app->getModule(ClassHelper::getMatchModule($this, false, '/'));
            $ns = ClassHelper::getNamespace($module);
            /** @var ActiveRecord $modelClass */
            $this->modelClass = $ns . '\models\orm\\' . str_replace('Finder', '', ClassHelper::getShortName($this));
            if (!class_exists($this->modelClass)) {
                /** @var ActiveRecord $modelClass */
                $this->modelClass = 'app\common\logic\orm\\' . str_replace('Finder', '', ClassHelper::getShortName($this));
                if (!class_exists($this->modelClass)) {
                    throw new InvalidValueException("Orm `{$this->modelClass}` is not found");
                }
            }
        }
        /** @var ActiveRecordInterface $modelClass */
        $modelClass = $this->modelClass;
        if (!ClassHelper::implementsInterface($modelClass, 'yii\db\ActiveRecordInterface')) {
            throw new InvalidCallException('modelClass in not extended ActiveRecordInterface'); // normalize message
        }
        $this->query = $modelClass::find();
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function search(array $params = [])
    {
        $this->load($params);
        $this->setAttributes($params);
        $this->initCondition();
        // todo VALIDATE
        return $this->provider();
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = parent::fields();
        unset($fields['query']);
        unset($fields['modelClass']);
        unset($fields['provider']);

        return $fields;
    }
}
