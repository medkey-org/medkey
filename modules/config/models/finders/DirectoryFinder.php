<?php
namespace app\modules\config\models\finders;

use app\common\base\Model;

/**
 * Class DirectoryFinder
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class DirectoryFinder extends Model
{
    /**
     * @var string
     */
    public $key;
    /**
     * @var string
     */
    public $label;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['key', 'label'], 'string', 'on' => ['search'] ]
        ];
    }
}
