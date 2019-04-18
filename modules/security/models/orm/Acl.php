<?php
namespace app\modules\security\models\orm;

use app\common\db\ActiveQuery;
use app\common\db\ActiveRecord;
use app\common\acl\Acl as AclCore;
use app\common\validators\ForeignKeyValidator;
use app\modules\security\SecurityModule;

/**
 * Class AccessAcl
 *
 * @property int $type
 * @property int $permission
 * @property AclRole $role
 *
 * @package Module\Security
 * @copyright 2012-2019 Medkey
 */
class Acl extends ActiveRecord
{
    const TYPE_SERVICE = AclCore::TYPE_SERVICE;
//    const TYPE_WORKFLOW = 2; // @todo deprecated

    const RULE_AUTHOR = 0x01;
    const RULE_RESPONSIBILITY = 0x02;
    const RULE_POSITION = 0x04;
    const RULE_DEPARTMENT = 0x08;
//    const RULE_RESPONSIBILITY_HEAD = ;

    /**
     * @return array
     */
    public static function aclRules()
    {
        return [
            self::RULE_AUTHOR => SecurityModule::t('acl', 'Author'),
            self::RULE_RESPONSIBILITY => SecurityModule::t('acl', 'Responsible'),
            self::RULE_POSITION => SecurityModule::t('acl', 'Position'),
            self::RULE_DEPARTMENT => SecurityModule::t('acl', 'Department'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        $result = null;
        if (!empty($this->rule) && is_array($this->rule)) {
            foreach ($this->rule as $r) {
                $result |= $r;
            }
            $this->rule = $result;
        }
        return true;
    }

    public function afterSave($insert, $changedAttributes)
    {
        $rules = static::aclRules();
        $result = [];
        if (!empty($this->rule)) {
            foreach ($rules as $key => $r) {
                if ($this->rule & $key) {
                    $result[] = $key;
                }
            }
            $this->rule = $result;
        }
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        $rules = static::aclRules();
        $result = [];
        if (!empty($this->rule)) {
            foreach ($rules as $key => $r) {
                if ($this->rule & $key) {
                    $result[] = $key;
                }
            }
            $this->rule = $result;
        }
        parent::afterFind();
    }

    /**
     * @inheritdoc
     */
    public static function modelIdentity()
    {
        return ['module', 'type', 'entity_type', 'acl_role_id', 'action'];
    }

    /**
     * @return array
     */
    public static function types()
    {
        return [
            static::TYPE_SERVICE => 'Service',
//            static::TYPE_WORKFLOW => 'Конечный автомат', // @todo DEPRECATED! УДАЛИТЬ этот тип
        ];
    }

    public function getTypeName()
    {
        $types = $this::types();
        return !isset($types[$this->type]) ?: $types[$this->type];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['module'],
                'unique',
                'filter' => function (ActiveQuery $query) {
                    return $query
                        ->notDeleted();
                },
                'targetAttribute' => ['module', 'type', 'entity_type', 'acl_role_id', 'action']
            ],
            [ ['type',],
                'integer'
            ],
            [ ['module', 'entity_type', 'acl_role_id', 'action'], 'required' ],
            [ [
                'module',
                'entity_type',
                'criteria_formula'
            ],
                'string'
            ],
            [
                [ 'entity_id', 'acl_role_id' ], ForeignKeyValidator::class,
            ],
//            [ ['action'],
//                'each',
//                'when' => function ($model) {
//                    return is_array($model->permission);
//                },
//                'rule' => ['integer']
//            ],
            [
                ['rule'],
                'each',
                'when' => function ($model) { // for only validation
                    return is_array($model->rule);
                },
                'rule' => ['integer']
            ]
        ];
    }

    /**
     * @return string
     */
    public function getRuleName()
    {
        $rules  = $this::aclRules();
        $result = '';
        if (!empty($this->rule) && is_array($this->rule)) {
            foreach ($this->rule as $key) {
                $result .= $rules[$key] . ' ';
            }
        }
        return $result;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAclRole()
    {
        return $this->hasOne(AclRole::class, ['id' => 'acl_role_id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabelsOverride()
    {
        return [
            'module' => SecurityModule::t('acl', 'Module'),
            'entity_type' => SecurityModule::t('acl', 'Resource'),
            'action' => SecurityModule::t('acl', 'Privilege'),
            'type' => SecurityModule::t('acl', 'Type'),
            'acl_role_id' => SecurityModule::t('acl', 'Role'),
            'rule' => SecurityModule::t('acl', 'Business-rule'),
        ];
    }
}
