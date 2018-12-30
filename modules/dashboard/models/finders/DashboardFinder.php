<?php
namespace app\modules\dashboard\models\finders;

use app\common\data\ActiveDataProvider;
use app\common\db\OrmFinder;
use app\modules\dashboard\models\orm\Dashboard;

/**
 * Class DashboardFinder
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class DashboardFinder extends OrmFinder
{
    /**
     * @var string
     */
    public $q;
    /**
     * @var \app\common\db\ActiveQuery
     */
    public $query;
    /**
     * @var array
     */
    public $types = [];


    /**
     * @return \yii\db\ActiveQuery
     */
    public function createQuery()
    {
        $this->query = Dashboard::find();

        return $this->query;
    }

    /**
     * @return void
     */
    public function createConditions()
    {
        if (!empty($this->query)) {
            $this->query->joinWith = null;
            $this->query->where = null;

            if ($this->q){
                $this->query->andWhere([
                    'like', Dashboard::tableColumns('title'), $this->q,
                ]);
            }

            $this->query->filterWhere([
                Dashboard::tableColumns('type') => $this->types,
            ]);
        }
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params = [])
    {
        parent::search();

        return new ActiveDataProvider([
            'query' => $this->query,
            'pagination' => [
                'pageSize' => 20,
            ]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['q'], 'string', 'on' => 'search' ],
            [ ['types'], 'integer', 'on' => 'search' ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'q' => 'Текст',
            'types' => 'Тип',
        ];
    }
}