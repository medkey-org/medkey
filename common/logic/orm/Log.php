<?php
namespace app\common\logic\orm;

use app\common\db\ActiveRecord;

/**
 * Class Log
 *
 * @property integer $level
 * @property string $category
 * @property string $prefix
 * @property string $message
 *
 * @package Common\Logic
 * @copyright 2012-2019 Medkey
 */
class Log extends ActiveRecord
{
    /**
     * @var bool
     */
    public $history = false;
    /**
     * @var array
     */
    public $levelLabels = [
        1 => 'error',
        4 => 'warning'
    ];


    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%log}}';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [ ['level', 'category', 'prefix', 'message'], 'required', 'on' => ['create', 'validate'] ],
            [ ['category', 'prefix', 'message'], 'string', 'on' => ['create', 'validate'] ],
            [ ['level'], 'integer', 'on' => ['create', 'validate'] ],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabelsOverride()
    {
        return [
            'level' => 'Category',
            'category' => 'Level',
            'prefix' => 'Prefix',
            'message' => 'Message',
            'created_at' => 'Event date',
            'user_created' => 'User',
            'userCreated' => 'User',
        ];
    }
}
