<?php
namespace app\modules\location\models\finders;

use app\common\base\Model;

/**
 * Class LocationFinder
 * @package Module\Location
 * @copyright 2012-2019 Medkey
 */
class LocationFinder extends Model
{
    /**
     * @var string
     */
    public $code;
    /**
     * @var string
     */
    public $description;
    /**
     * @var string
     */
    public $endDate;
    /**
     * @var string
     */
    public $startDate;
    /**
     * @var integer
     */
    public $status;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['status'], 'integer', 'on' => 'search' ],
            [ ['code', 'description', 'endDate', 'startDate'], 'string', 'on' => 'search' ],
        ];
    }
}
