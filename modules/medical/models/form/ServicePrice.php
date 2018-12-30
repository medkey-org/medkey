<?php
namespace app\modules\medical\models\form;

use app\common\base\Model;
use app\common\validators\ForeignKeyValidator;
use app\modules\medical\models\orm\ServicePrice as ServicePriceORM;

/**
 * Class ServicePrice
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class ServicePrice extends Model
{
    public $id;
    public $cost;
    public $service_id;
    public $service_price_list_id;
    public $status;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['status'], 'integer' ],
            [ ['service_id'], 'required' ],
            [ ['service_id', 'service_price_list_id'], ForeignKeyValidator::class ],
            [ ['service_id'], 'validateUniqueService', 'on' => 'create' ],
            [ 'cost', 'double'],
            [ 'cost', 'default', 'value' => '0.00' ],
        ];
    }

    /**
     * @param $attribute
     * @return void
     */
    public function validateUniqueService($attribute)
    {
        $servicePrice = ServicePriceORM::find()
            ->where([
                'service_id' => $this->{$attribute},
                'service_price_list_id' => $this->service_price_list_id,
            ])
            ->notDeleted()
            ->one();
        if (isset($servicePrice)) {
            $this->addError($attribute, 'Указанная услуга в текущем прайсе уже существует!');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cost' => 'Стоимость',
            'service_id' => 'Услуга',
            'service_price_list_id' => 'Прайс-лист',
            'status' => 'Статус',
        ];
    }
}
