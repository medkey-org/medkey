<?php
namespace app\modules\location\port\rest\controllers;

use app\common\rest\ActiveController;
use app\common\data\ActiveDataProvider;
use app\modules\location\models\orm\Location;
use yii\db\ActiveRecordInterface;

/**
 * Class LocationController
 * @package Module\Location
 * @copyright 2012-2019 Medkey
 */
class LocationController extends ActiveController
{
    /**
     * @var string the model class name. This property must be set.
     */
    public $modelClass = Location::class;


    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [];
    }

    /**
     * @param string $q
     * @param int $page
     * @return ActiveDataProvider
     */
    public function actionIndex($q, $page)
    {
        $page = (int)$page;
        /* @var $modelClass ActiveRecordInterface */
        $modelClass = $this->modelClass;
        $query = $modelClass::find()
            ->where([
                'like',
                'description',
                $q
            ]);
        /** @var ActiveDataProvider $provider */
        $provider = \Yii::createObject([
            'class' => ActiveDataProvider::className(),
            'query' => $query,
            'pagination' => [
                'page' => --$page,
                'pageSize' => 10
            ]
        ]);
        return $provider;
    }
}
