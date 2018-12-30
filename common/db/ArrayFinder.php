<?php
namespace app\common\db;

/**
 * Class ArrayFinder
 * @package Common\DB
 * @copyright 2012-2019 Medkey
 */
class ArrayFinder extends BaseFinder
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function search(array $params = [])
    {
        $this->load($params);
        $this->setAttributes($params);

        return $this->provider();
    }
}
