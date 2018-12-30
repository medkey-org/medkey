<?php
namespace app\common\logic\orm;

use app\common\db\ActiveRecord;
use yii\db\ActiveQueryInterface;

/**
 * Class Catalog
 * @package Common\Logic
 * @copyright 2012-2019 Medkey
 */
class Catalog extends ActiveRecord
{
    /**
     * @return ActiveQueryInterface
     */
    public function getCategories()
    {
        return $this->hasMany(Category::className(), ['category_id' => 'id']);
    }
}
