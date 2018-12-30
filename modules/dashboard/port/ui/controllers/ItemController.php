<?php
namespace app\modules\dashboard\port\ui\controllers;

use app\common\logic\orm\DashboardItem;
use app\common\web\CrudController;

/**
 * Class ItemController
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class ItemController extends CrudController
{
    public $modelClass = '\app\modules\dashboard\models\orm\DashboardItem';
}
