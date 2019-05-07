<?php
namespace app\modules\medical\port\ui\controllers;

use app\common\web\Controller;
use app\modules\medical\models\orm\Ehr;
use app\modules\medical\models\orm\EhrRecord;

/**
 * Class EhrRecordController
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class EhrRecordController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actionView($ehrId, $id = null)
    {
        $model = EhrRecord::ensureWeak($id);
        $ehr = Ehr::ensure($ehrId);
        return $this->render('view', [
            'model' => $model,
            'ehr' => $ehr,
        ]);
    }
}
