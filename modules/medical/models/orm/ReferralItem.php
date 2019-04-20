<?php
namespace app\modules\medical\models\orm;

use app\common\db\ActiveRecord;
use app\common\validators\ForeignKeyValidator;

/**
 * Class ReferralItem
 *
 * @property string $referral_id
 * @property string $service_id
 *
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ReferralItem extends ActiveRecord
{
    public static function modelIdentity()
    {
        return ['referral_id', 'service_id'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ [ 'referral_id', 'service_id' ], ForeignKeyValidator::class ]
        ];
    }

    public function getReferral()
    {
        return $this->hasOne(Referral::class, ['id' => 'referral_id']);
    }

    public function getService()
    {
        return $this->hasOne(Service::class, ['id' => 'service_id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabelsOverride()
    {
        return [
            'service_id' => 'Медицинская услуга',
            'referral_id' => 'Направление',
        ];
    }
}
