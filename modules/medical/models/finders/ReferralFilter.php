<?php
namespace app\modules\medical\models\finders;

use app\common\base\Model;
use app\common\validators\ForeignKeyValidator;

/**
 * Class ReferralFinder
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ReferralFilter extends Model
{
    public $orderId;
    public $ehrId;
    public $status;
    public $number;
    public $updatedAt;
    public $patientId;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['orderId', 'ehrId', 'patientId', ], ForeignKeyValidator::class ],
            [ ['status', 'number', 'updatedAt', ], 'string', 'on' => 'search' ],
        ];
    }
}
