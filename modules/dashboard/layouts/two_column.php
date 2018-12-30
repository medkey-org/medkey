<?php

use app\common\helpers\Html;

/**
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */

echo Html::row([[
    'content' => Html::col(((isset($dashlets[0])) ? $dashlets[0] : []), ['size' => 12])
], [
    'content' => Html::col(((isset($dashlets[1])) ? $dashlets[1] : []), ['size' => 12])
]]);