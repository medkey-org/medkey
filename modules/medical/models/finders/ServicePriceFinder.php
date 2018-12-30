<?php
namespace app\modules\medical\models\finders;

use app\common\base\Model;
use app\common\validators\ForeignKeyValidator;

/**
 * Class ServicePriceFinder
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ServicePriceFinder extends Model
{
    public $id;
    public $cost;
    public $service_id;
    public $service_price_list_id;
    public $status;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['cost', 'status'], 'integer', 'on' => 'search' ],
            [ ['service_id', 'service_price_list_id'], ForeignKeyValidator::class, 'on' => 'search' ],
        ];
    }
}
