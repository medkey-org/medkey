<?php
namespace app\modules\location\application;

use app\modules\location\models\finders\LocationFinder;

/**
 * Interface LocationServiceInterface
 * @package Module\Location
 * @copyright 2012-2019 Medkey
 */
interface LocationServiceInterface
{
    public function getLocationList(LocationFinder $form);
}
