<?php
namespace app\modules\security\models\form;

use app\common\base\Model;
use app\modules\security\models\orm\User;
use app\modules\security\SecurityModule;
use yii\web\IdentityInterface;

/**
 * Class LoginForm
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class LoginForm extends Model
{
    /**
     * @var string
     */
    public $login;
    /**
     * @var string
     */
    public $password;
    /**
     * @var IdentityInterface
     */
    private $_identity;


    /**
     * @return mixed
     */
    public function getIdentity()
    {
        return $this->_identity;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['login', 'password'], 'required' ],
            [ ['login', 'password'], 'string' ],
            [ ['login'], 'validateUser' ],
        ];
    }

    /**
     * @param string $attribute
     * @return void
     */
    public function validateUser($attribute)
    {
        $user = User::find()
            ->setAccess(false)
            ->where([
                'login' => $this->login,
                'status' => User::STATUS_ACTIVE,
            ])
            ->notDeleted()
            ->one();
        $this->_identity = $user;
        if (
            $user === null
            || !\Yii::$app->getSecurity()->validatePassword($this->password, $user->password_hash)
        ) {
            $this->addError($attribute, 'Incorrect password');
        }
    }

    public function attributeLabels()
    {
        return [
            'login' => SecurityModule::t('common','Login'),
            'password' => SecurityModule::t('common','Password'),
        ];
    }
}
