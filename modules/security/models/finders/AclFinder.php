<?php
namespace app\modules\security\models\finders;

use app\common\base\Model;

/**
 * Class AccessAclFinder
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class AclFinder extends Model
{
    public $type;
    public $updateAt;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['type'], 'integer', 'on' => 'search' ],
            [ ['updated_at'], 'string', 'on' => 'search' ]
        ];
    }
}
