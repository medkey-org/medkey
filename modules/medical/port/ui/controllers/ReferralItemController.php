<?php
namespace app\modules\medical\port\ui\controllers;

use app\common\web\ScreenController;
use app\modules\medical\models\orm\ReferralItem;

/**
 * Class ReferralItemController
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ReferralItemController extends ScreenController
{
    public $modelClass = ReferralItem::class;
}
