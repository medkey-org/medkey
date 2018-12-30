<?php
namespace app\modules\config\models\finders;

use app\common\base\Model;

/**
 * Class WorkflowFinder
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class WorkflowFinder extends Model
{
    public $ormClass;
    public $name;
    public $type;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['ormClass', 'name'], 'string' ],
            [ ['type'], 'integer' ]
        ];
    }
}
