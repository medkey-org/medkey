<?php
namespace app\modules\medical\models\finders;

use app\common\base\Model;
use app\common\helpers\CommonHelper;

/**
 * Class ServicePriceListFinder
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ServicePriceListFinder extends Model
{
    public $name;
    public $status;
    public $start_date;
    public $end_date;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['name'], 'string', 'on' => 'search' ],
            [ ['status'], 'integer', 'on' => 'search' ],
            [ ['start_date', 'end_date'],
                'date',
                'format' => CommonHelper::FORMAT_DATE_UI,
                'on' => 'search',
            ],
        ];
    }
}
