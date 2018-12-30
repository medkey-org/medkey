<?php
namespace app\modules\medical\port\ui\controllers;

use app\common\web\ScreenController;
use app\modules\medical\models\orm\Service;

/**
 * Class ServiceController
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ServiceController extends ScreenController
{
    public $modelClass = Service::class;
}
