<?php
namespace app\modules\medical\models\orm;

use app\common\db\ActiveQuery;
use app\common\db\ActiveRecord;
use app\common\dto\Dto;
use app\common\helpers\CommonHelper;
use app\common\helpers\Json;
use app\common\validators\ForeignKeyValidator;
use app\modules\medical\MedicalModule;
use yii\base\InvalidValueException;

/**
 * Class Referral
 *
 * @property string $number
 * @property string description
 * @property int $status
 * @property string $ehr_id
 *
 * @package Module\Medical
 * @copyright 2012-2019 Medkey
 */
class Referral extends ActiveRecord
{
    const STATUS_NEW = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_WORKING = 3;
    const STATUS_WORKED = 4;
    const STATUS_CLOSED = 5;
    /**
     * Типы направлений
     */
    const TYPE_INVALID = 0;
    const TYPE_ANALYSIS = 1;
    const TYPE_EXAMINATION = 2;
    const TYPE_OPERATION = 3;
    const TYPE_HOSPITALIZATION = 4;

    public static function modelIdentity()
    {
        return ['number'];
    }

    public function init()
    {
        if ($this->isNewRecord) {
            $this->number = $this->generateNumber();
        }
        parent::init();
    }

    /**
     * @todo number в БД является строкой
     * @todo лучше SEQUENCE на уровне БД делать
     * @return int
     */
    public function generateNumber()
    {
        $db = \Yii::$app->db;
        if ($db->driverName === 'pgsql') {
            $cast = 'cast(number as INT)';
        } elseif ($db->driverName === 'mysql') {
            $cast = 'cast(number as SIGNED)';
        } else {
            $cast = null;
        }
        $max = static::find()
            ->max($cast);
        return (string)++$max;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [ 'number', 'unique', 'filter' => function (ActiveQuery $query) {
                return $query
                    ->notDeleted();
            }, ],
            [ ['ehr_id'], ForeignKeyValidator::class, ],
            [ [ 'number', 'description' ],
                'string',
                'on' => [ActiveRecord::SCENARIO_CREATE, ActiveRecord::SCENARIO_UPDATE]
            ],
            [ ['number'], 'default', 'value' => function () {
                return $this->generateNumber();
            } ],
            [
                'status', 'default', 'value' => self::STATUS_NEW,
            ],
//            [ ['number'], 'filter', 'filter' => function ($value) {
//                if (!empty($value)) {
//                    return $value;
//                }
//                $value = $this->generateNumber();
//                return $value;
//            },
//                'on' => ActiveRecord::SCENARIO_CREATE
//            ],
            [ ['number', 'ehr_id'], 'required' ],
            [ ['status'],
                'integer',
                'on' => [ActiveRecord::SCENARIO_CREATE, ActiveRecord::SCENARIO_UPDATE]
            ],
            [ 'start_date', 'default', 'value' => function () {
                return \Yii::$app->formatter->asDate(time(), CommonHelper::FORMAT_DATE_DB);
            } ],
            [ 'start_date', 'filter', 'filter' => function ($value) {
                return $this->start_date = \Yii::$app->formatter->asDate(empty($value) ? null : $value, CommonHelper::FORMAT_DATE_DB);
            },
                'on' => [ActiveRecord::SCENARIO_CREATE, ActiveRecord::SCENARIO_UPDATE]
            ],
            [ 'end_date', 'filter', 'filter' => function ($value) {
                return $this->end_date = \Yii::$app->formatter->asDate(empty($value) ? null : $value, CommonHelper::FORMAT_DATE_DB);
            },
                'on' => [ActiveRecord::SCENARIO_CREATE, ActiveRecord::SCENARIO_UPDATE]
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEhr()
    {
        return $this->hasOne(Ehr::class, ['id' => 'ehr_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReferralItems()
    {
        return $this->hasMany(ReferralItem::class, ['referral_id' => 'id']);
    }

    /**
     * @return array
     */
    public static function statuses()
    {
        return [
            static::STATUS_NEW => MedicalModule::t('referral','New'),
            static::STATUS_ACTIVE => MedicalModule::t('referral','Active'),
            static::STATUS_WORKING => MedicalModule::t('referral','In work'),
            static::STATUS_WORKED => MedicalModule::t('referral','Completed'),
            static::STATUS_CLOSED => MedicalModule::t('referral','Closed'),
        ];
    }

    public function isNew()
    {
        return $this->status === self::STATUS_NEW;
    }

    /**
     * @inheritdoc
     */
    public function generateReferral(string $ehrId, string $orderId, array $services)
    {
        if (!$this->isNewRecord) {
            throw new \DomainException(MedicalModule::t('referral', 'Incorrect referral data or referral already exists'));
        }
        if (!is_array($services) || empty($services)) {
            throw new InvalidValueException(MedicalModule::t('referral', 'Incorrect referral data or referral already exists'));
        }
        $this->ehr_id = $ehrId;
        $this->order_id = $orderId;
        $this->status = self::STATUS_ACTIVE; // На основе заказа уже готовое направление к работе с ним. Редактировать его нельзя
        if (!$this->save()) {
            throw new \DomainException(MedicalModule::t('referral', 'Cannot save referral') . ': ' . Json::encode($this->getErrors()));
        }
        foreach ($services as $serviceId) {
            $rItem = new ReferralItem([
                'scenario' => ActiveRecord::SCENARIO_CREATE,
            ]);
            $rItem->referral_id = $this->id;
            $rItem->service_id = $serviceId;
            if (!$rItem->save()) {
                throw new \DomainException(MedicalModule::t('referral', 'Cannot save referral services') . ': ' . Json::encode($rItem->getErrors()));
            }
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        $statuses  = $this::statuses();

        return !empty($statuses[$this->status]) ? $statuses[$this->status] : '';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabelsOverride()
    {
        return [
            'number' => MedicalModule::t('referral', 'Number'),
            'description' => MedicalModule::t('referral','Description'),
            'status' => MedicalModule::t('referral', 'Status'),
            'start_date' => MedicalModule::t('referral', 'Start date'),
            'end_date' => MedicalModule::t('referral', 'End date'),
            'ehr_id' => MedicalModule::t('referral', 'EHR'),
            'order_id' => MedicalModule::t('referral', 'Order'),
            'patient_id' => MedicalModule::t('referral', 'Patient'),
        ];
    }
}
