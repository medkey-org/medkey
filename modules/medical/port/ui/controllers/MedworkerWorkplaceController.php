<?php
namespace app\modules\medical\port\ui\controllers;

use app\common\web\ScreenController;

/**
 * Class MedworkerWorkplaceController
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class MedworkerWorkplaceController extends ScreenController
{
    /**
     * @inheritdoc
     */
    public function actionView($id = null)
    {
        return $this->render('view');
    }
}
