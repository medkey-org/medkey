<?php
namespace app\modules\dashboard\models\finders;

use app\common\data\ActiveDataProvider;
use app\common\db\OrmFinder;
use app\modules\dashboard\models\orm\DashboardItem;

/**
 * Class DashboardItemFinder
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class DashboardItemFinder extends OrmFinder
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
     * @var integer
     */
    public $dashboardId;


    /**
     * @return \yii\db\ActiveQuery
     */
    public function createQuery()
    {
        $this->query = DashboardItem::find();

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

            if ($this->dashboardId){
                $this->query->andWhere([
                    '=', DashboardItem::tableColumns('dashboard_id'), $this->dashboardId,
                ]);
            }
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
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'q' => 'Текст',
        ];
    }
}
