<?php
namespace app\modules\medical\port\ui\controllers;

use app\common\db\ActiveRecord;
use app\common\web\ScreenController;
use app\modules\medical\models\orm\Ehr;

/**
 * Class EhrController
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class EhrController extends ScreenController
{
    public $modelClass = Ehr::class;


    /**
     * @inheritdoc
     */
    public function actionView($id = null)
    {
        $modelClass = $this->modelClass;
        /** @var $modelClass ActiveRecord */
        $model = $modelClass::ensureWeak($id);
        empty(\Yii::$app->request->get('patientId')) ?: $model->patient_id = \Yii::$app->request->get('patientId');
        return $this->render('view', [
            'model' => $model,
        ]);
    }
}
