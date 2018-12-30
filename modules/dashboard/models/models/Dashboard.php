<?php
namespace app\modules\dashboard\models\models;

use app\common\ddd\AggregateRootInterface;

/**
 * Class Dashboard
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class Dashboard implements AggregateRootInterface
{
    public $key;
    public $title;
    public $description;
    public $layout;
    public $type;
    public $ownerId;
}
