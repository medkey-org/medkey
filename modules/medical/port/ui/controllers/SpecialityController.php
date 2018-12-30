<?php
namespace app\modules\medical\port\ui\controllers;

use app\common\web\ScreenController;
use app\modules\medical\models\orm\Speciality;

/**
 * Class SpecialityController
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class SpecialityController extends ScreenController
{
    public $modelClass = Speciality::class;
}
