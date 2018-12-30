<?php
namespace app\modules\security\models\finders;

use app\common\base\Model;

/**
 * Class UserFinder
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class UserFinder extends Model
{
    public $login;
    public $roleName;
    public $updatedAt;
    public $status;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ 'status', 'integer' ],
            [ ['updatedAt', 'login', 'roleName'], 'string', 'on' => 'search' ]
        ];
    }
}
