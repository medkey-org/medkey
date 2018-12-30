<?php
namespace app\modules\organization\models\finders;

use app\common\base\Model;
use app\common\helpers\CommonHelper;

/**
 * Class EmployeeFinder
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */
class EmployeeFinder extends Model
{
    public $ids;
    public $updatedAt;
    /**
     * @var string
     */
    public $lastName;
    /**
     * @var string
     */
    public $firstName;
    /**
     * @var string
     */
    public $middleName;
    /**
     * @var string
     */
    public $birthday;
    /**
     * @var int
     */
    public $sex;
    /**
     * @var string
     */
    public $fullName;
    /**
     * @var string
     */
    public $phone;
    /**
     * @var int
     */
    public $userId;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ 'updatedAt', 'string', 'on' => 'search' ],
            [ ['fullName', 'phone'], 'string', 'on' => 'search' ],
            [ ['sex'], 'integer'],
            [ ['birthday'], 'date', 'format' => CommonHelper::FORMAT_DATE_UI,
                'on' => 'search'
            ],
        ];
    }
}
