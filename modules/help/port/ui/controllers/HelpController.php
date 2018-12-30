<?php
namespace app\modules\help\port\ui\controllers;

use app\common\web\Controller;

/**
 * Help controller
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class HelpController extends Controller
{
    /**
     * Show information about application version
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
