<?php
namespace app\modules\help\port\ui\controllers;

use app\common\web\Controller;

/**
 * ServiceController
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class ServiceController extends Controller
{
    /**
     * Show information about instance configuration
     * @return string
     */
    public function actionInstance()
    {
        return $this->render('instance');
    }

    /**
     * Show information about instance data statistics
     * @return string
     */
    public function actionStatistics()
    {
        return $this->render('statistics');
    }
}
