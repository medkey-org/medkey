<?php
namespace app\seeds;

use app\common\helpers\ArrayHelper;
use app\common\seeds\Seed;
use app\modules\security\models\orm\User as UserOrm;

class User extends Seed
{
	public function run()
	{
        $aclRoles = $this->call('acl_role_seed')->models;
		$this->model = UserOrm::class;

		$this->data = [
		    [
		        'login' => 'admin',
                'password_hash' => \Yii::$app->security->generatePasswordHash(getenv('SUPER_PASSWORD')),
                'status' => UserOrm::STATUS_ACTIVE,
                'acl_role_id' => ArrayHelper::findBy($aclRoles, ['name' => 'admin'])->id,
            ],
        ];
	}
}
