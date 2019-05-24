<?php
namespace app\modules\medical\port\ui\controllers;

use app\common\web\Controller;
use app\modules\medical\models\orm\Speciality;

/**
 * Class SpecialityController
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class SpecialityController extends Controller
{
    public $modelClass = Speciality::class;
}
