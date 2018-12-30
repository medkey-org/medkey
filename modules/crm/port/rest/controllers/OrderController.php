<?php
namespace app\modules\crm\port\rest\controllers;

use app\common\data\ActiveDataProvider;
use app\common\rest\ActiveController;
use app\modules\crm\models\orm\Order;
use yii\db\ActiveRecordInterface;

/**
 * Class OrderController
 * @package Module\CRM
 * @copyright 2012-2019 Medkey
 */
class OrderController extends ActiveController
{
    /**
     * @var string the model class name. This property must be set.
     */
    public $modelClass = Order::class;


    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [];
    }

    /**
     * @param string $q
     * @return ActiveDataProvider
     */
    public function actionIndex($q)
    {
        /* @var $modelClass ActiveRecordInterface */
        $modelClass = $this->modelClass;
        $query = $modelClass::find()
            ->where([
                'like',
                'number',
                $q
            ]);
        /** @var ActiveDataProvider $provider */
        $provider = \Yii::createObject([
            'class' => ActiveDataProvider::className(),
            'query' => $query
        ]);
        return $provider;
    }
}
