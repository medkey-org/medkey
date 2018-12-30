<?php
namespace app\common\db;

use app\common\helpers\ClassHelper;
use yii\base\InvalidParamException;
use yii\db\QueryInterface;

/**
 * Class ArrayFinder
 * @package Common\DB
 * @copyright 2012-2019 Medkey
 * @deprecated
 */
class QueryFinder extends BaseFinder
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $tableName = mb_strtolower(str_replace('Finder', '', ClassHelper::getShortName(static::className())));
        if (!isset($this->query)) {
            $this->query = (new Query())->from('{{%' . $tableName . '}}');
        }
        if (!($this->query instanceof QueryInterface)) {
            throw new InvalidParamException(\Yii::t('app', 'Invalid query param in Finder')); // todo normalize English
        }
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

        return $this->provider();
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = parent::fields();
        unset($fields['query']);
        unset($fields['provider']);

        return $fields;
    }
}
