<?php
namespace app\modules\medical\models\orm;

use app\common\db\ActiveRecord;
use app\common\validators\ForeignKeyValidator;
use app\modules\config\entities\CurrencyEntity;
use app\modules\medical\MedicalModule;

/**
 * Class ServicePrice
 *
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ServicePrice extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        $servicePriceList = ServicePriceList::findOneEx($this->service_price_list_id); // todo try catch
        if (!empty($servicePriceList->currency)) {
            $this->cost = CurrencyEntity::moneyDecode($this->cost, $servicePriceList->currency);
        }
        parent::afterFind();
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        $servicePriceList = ServicePriceList::findOneEx($this->service_price_list_id); // todo try catch
        if (!empty($servicePriceList->currency)) {
            $this->cost = CurrencyEntity::moneyDecode($this->cost, $servicePriceList->currency);
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function getService()
    {
        return $this->hasOne(Service::class, ['id' => 'service_id']);
    }

    public function getServicePriceList()
    {
        return $this->hasOne(ServicePriceList::class, ['id' => 'service_price_list_id']);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $servicePriceList = ServicePriceList::findOneEx($this->service_price_list_id); // todo try catch
        if (!empty($servicePriceList->currency)) {
            $this->cost = CurrencyEntity::moneyEncode($this->cost, $servicePriceList->currency);
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ 'status', 'default', 'value' => self::STATUS_ACTIVE ],
            [ ['status'], 'integer' ],
            [ ['service_id'], 'required' ],
            [ ['service_id', 'service_price_list_id'], ForeignKeyValidator::class, ],
            [ ['service_id'], 'validateUniqueService', 'on' => 'create' ],
            [ 'cost', 'double'],
            [ 'cost', 'default', 'value' => '0.00' ],
        ];
    }

    /**
     * @todo просто скопировал с формы чтобы была обратная совместимость по коду (вместо UniqueValidator)
     * @param $attribute
     * @return void
     */
    public function validateUniqueService($attribute)
    {
        $servicePrice = static::find()
            ->where([
                'service_id' => $this->{$attribute},
                'service_price_list_id' => $this->service_price_list_id,
            ])
            ->notDeleted()
            ->one();
        if (isset($servicePrice)) {
            $this->addError($attribute, MedicalModule::t('servicePrice', 'Given service already exists in price list'));
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabelsOverride()
    {
        return [
            'cost' => MedicalModule::t('servicePrice', 'Cost'),
            'service_id' => MedicalModule::t('servicePrice', 'Service'),
            'service_price_list_id' => MedicalModule::t('servicePrice', 'Price list'),
            'status' => MedicalModule::t('servicePrice', 'Status'),
        ];
    }
}
