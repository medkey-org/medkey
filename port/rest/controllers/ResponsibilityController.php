<?php
namespace app\port\rest\controllers;

use app\common\base\Module;
use app\common\db\ActiveRecord;
use app\common\db\ResponsibilityEntityInterface;
use app\common\filters\QueryParamAuth;
use app\common\rest\Controller;
use app\modules\organization\models\orm\Employee;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;

/**
 * Class ResponsibilityController
 * @package Common\REST
 * @copyright 2012-2019 Medkey
 */
class ResponsibilityController extends Controller
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
        $module = \Yii::$app->request->post('module');
        $entity = \Yii::$app->request->post('entity');
        $entityId = \Yii::$app->request->post('entity_id');
        $responsibilities = \Yii::$app->request->post('responsibilities');
        /** @var Module $module */
        $module = \Yii::$app->getModule($module);
        $ns = $module->getOrmNamespace();
        /** @var ActiveRecord $modelClass */
        $modelClass = $ns . '\\' . $entity;
        if (!class_exists($modelClass)) {
            throw new HttpException(400, 'Не хватает параметров для обработки запроса.');
        }
        $model = $modelClass::ensure($entityId);
        if (!$model instanceof ResponsibilityEntityInterface) {
            throw new HttpException(400, 'Модель сущности не соответствует интерфейсу.');
        }
        $model->unlinkAll('responsibilityEmployees', true);
        if (!is_array($responsibilities)) {
            return $model;
        }
        foreach ($responsibilities as $res) {
            $employee = Employee::findOneEx($res);
            $model->link('responsibilityEmployees', $employee);
        }
        return $this->asJson($model);
    }
}
