<?php
namespace app\modules\medical\models\finders;

use app\common\base\Model;
use app\common\validators\ForeignKeyValidator;

/**
 * Class EhrFinder
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class EhrFilter extends Model
{
    public $patientId;
    public $number;
    public $type;
    public $updatedAt;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['patientId'], ForeignKeyValidator::class ],
            [ ['number', 'type', 'updatedAt'], 'string', 'on' => 'search' ]
        ];
    }
}
