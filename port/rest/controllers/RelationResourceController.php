<?php
namespace app\port\rest\controllers;

use app\common\db\ActiveRecord;
use app\common\filters\QueryParamAuth;
use app\common\helpers\ClassHelper;
use app\common\rest\Controller;
use yii\db\ActiveRecordInterface;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;

/**
 * Class RelationResourceController
 * @package Common\REST
 * @copyright 2012-2019 Medkey
 */
class RelationResourceController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'authenticator' => [
                'class' => QueryParamAuth::class,
                'isSession' => false,
                'optional' => [
                    '*',
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function () {
                    throw new ForbiddenHttpException(\Yii::t('yii', 'You are not allowed to perform this action.'));
                }
            ],
        ]);
    }
    
    public function actionRelation()
    {
        $relationModelPk = \Yii::$app->request->post('RelationModelPk');
        $modelClass = \Yii::$app->request->post('ModelClass');
        $relationClass = \Yii::$app->request->post('RelationClass');
        $modelPk = \Yii::$app->request->post('ModelPk');
        $relationName = \Yii::$app->request->post('RelationName');
        if (
            !class_exists($modelClass)
            || !ClassHelper::implementsInterface($modelClass, ActiveRecordInterface::class)
            || !class_exists($relationClass)
            || !ClassHelper::implementsInterface($relationClass, ActiveRecordInterface::class)
        ) {
            throw new HttpException(400, 'Not enough params for query handling.');
        }
        /** @var ActiveRecord $modelClass */
        $model = $modelClass::findOneEx($modelPk);
        /** @var ActiveRecord $relationClass */
        $relationModel = $relationClass::findOneEx($relationModelPk);
        $model->unlink($relationName, $relationModel, true);
        $model->link($relationName, $relationModel);
        return $this->asJson($model);
    }
}
