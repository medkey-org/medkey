<?php
namespace app\modules\security\models\form;

use app\common\base\Model;
use app\common\validators\ForeignKeyValidator;
use app\modules\security\SecurityModule;
use yii\base\Security;

/**
 * Class UserPassword
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class UserPassword extends Model
{
    public $userId;
    public $password;
    public $passwordRepeat;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['userId'], ForeignKeyValidator::class, ],
            [ ['password', 'passwordRepeat'], 'string', 'min' => 5, 'max' => 150, ],
            [ ['password', 'passwordRepeat'], 'required' ],
            [ ['passwordRepeat'], 'compare', 'compareAttribute' => 'password', 'message' =>  SecurityModule::t('user', 'Passwords do not match') ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => SecurityModule::t('user', 'Password'),
            'passwordRepeat' => SecurityModule::t('user', 'Re-enter password'),
        ];
    }
}
