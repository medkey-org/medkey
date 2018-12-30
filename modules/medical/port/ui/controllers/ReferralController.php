<?php
namespace app\modules\medical\port\ui\controllers;

use app\common\db\ActiveRecord;
use app\common\web\ScreenController;
use app\modules\medical\models\orm\Referral;

/**
 * Class ReferralController
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ReferralController extends ScreenController
{
    public $modelClass = Referral::class;

    public function actionView($id = null)
    {
        $modelClass = $this->modelClass;
        /** @var $modelClass ActiveRecord */
        $model = $modelClass::ensureWeak($id);
        empty(\Yii::$app->request->get('ehrId')) ?: $model->ehr_id = \Yii::$app->request->get('ehrId');
        return $this->render('view', [
            'model' => $model,
        ]);
    }
}
