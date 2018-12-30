<?php
namespace app\modules\workplan\models\finders;

use app\common\base\Model;
use app\common\validators\ForeignKeyValidator;

/**
 * Class WorkplanFinder
 * @package Module\Workplan
 * @copyright 2012-2019 Medkey
 */
class WorkplanFilter extends Model
{
    /**
     * @var string
     */
    public $employeeId;
    public $updatedAt;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['employeeId', ForeignKeyValidator::class],
            [['updatedAt'], 'string', 'on' => 'search']
        ];
    }
}
