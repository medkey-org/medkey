<?php
namespace app\modules\medical\models\finders;

use app\common\base\Model;

/**
 * Class PatientFinder
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class PatientFilter extends Model
{
    public $fullName;
    public $birthday;
    public $updatedAt;
    public $snils;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['fullName', 'birthday', 'updatedAt', 'snils',], 'string', 'on' => 'search' ]
        ];
    }
}
