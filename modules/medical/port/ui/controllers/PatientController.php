<?php
namespace app\modules\medical\port\ui\controllers;

use app\common\web\ScreenController;
use app\modules\medical\models\orm\Patient;

/**
 * Class PatientController
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class PatientController extends ScreenController
{
    public $modelClass = Patient::class;
}
