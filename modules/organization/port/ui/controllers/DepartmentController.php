<?php
namespace app\modules\organization\port\ui\controllers;

use app\common\filters\QueryParamAuth;
use app\common\web\CrudController;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;

/**
 * Class DivisionController
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */
class DepartmentController extends CrudController
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
}
