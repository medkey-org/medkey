<?php
namespace app\common\db;

/**
 * Class ActiveQuery
 * @package Common\DB
 * @copyright 2012-2019 Medkey
 */
class ActiveQuery extends \yii\db\ActiveQuery
{
    /**
     * @deprecated удалить
     * @var bool
     */
    private $_access = true;

	/**
	 * @return ActiveQuery
	 */
	public function notDeleted()
	{
		$this->andWhere([
			call_user_func([$this->modelClass, 'tableColumns'], 'is_deleted') => ActiveRecord::IS_DELETE_FALSE,
		]);
		return $this;
	}

    /**
     * @param bool $value
     * @return $this
     */
	public function setAccess($value)
    {
        $this->_access = $value;
        return $this;
    }

    /**
     * @return bool
     */
    public function getAccess()
    {
        return $this->_access;
    }
}
