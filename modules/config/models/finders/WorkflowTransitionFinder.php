<?php
namespace app\modules\config\models\finders;

use app\common\base\Model;
use app\common\validators\ForeignKeyValidator;

/**
 * Class WorkflowTransitionFinder
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class WorkflowTransitionFinder extends Model
{
    public $updatedAt;
    public $workflowId;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['workflowId',], ForeignKeyValidator::class, ],
            [ ['updated_at'], 'string', 'on' => 'search' ]
        ];
    }
}
