<?php
namespace app\modules\dashboard\models\orm;

use app\common\db\ActiveQuery;
use app\common\db\ActiveRecord;
use app\common\ddd\AggregateRootInterface;
use app\common\rbac\ClassPermissionableTrait;
//use app\common\rbac\PermissionableTrait;
use app\modules\security\models\orm\User;

/**
 * Рабочий стол
 *
 * @property string     $key
 * @property string     $title
 * @property string     $description
 * @property string     $layout
 * @property integer    $type
 * @property integer    $owner_id
 *
 * @property-read User            $owner
 * @property-read DashboardItem[] $dashboardItems
 *
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class Dashboard extends ActiveRecord
{
    use ClassPermissionableTrait;

    const TYPE_TEMPLATE = 0;
    const TYPE_USER = 1;
    /**
     * @var string
     */
    public static $module = 'dashboard';


    /**
     * @return string
     */
    public static function entityName()
    {
        return 'Рабочий стол';
    }

    /**
     * @inheritdoc
     */
    public static function modelIdentity()
    {
        return ['key'];
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            self::TYPE_TEMPLATE,
            self::TYPE_USER,
        ];
    }

    /**
     * Коллекция лейблов для отображения текущего типа
     * @return array
     */
    public static function getTypeLabels()
    {
        return [
            static::TYPE_TEMPLATE => 'Шаблонный',
            static::TYPE_USER => 'Пользовательский'
        ];
    }

    /**
     * @return string
     */
    public function getTypeLabel()
    {
        $labels = static::getTypeLabels();

        if (!array_key_exists($this->type, $labels)){
            return 'Неизвестный';
        }
        return $labels[$this->type];
    }

    /**
     * Получить модель пользователя рабочего стола
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(User::className(), ['id' => 'owner_id']);
    }

	/**
	 * @return ActiveQuery
	 */
    public function getDashboardItems()
    {
        return $this->hasMany(DashboardItem::className(), ['dashboard_id' => 'id'])->orderBy('order');
    }

    /**
     * @return null|string
     */
    public function getOwnerFullName() {
        return $this->owner ? $this->owner->fullName : null;
    }

    /**
     * @return array
     */
    public static function getLayouts()
    {
        return \Yii::$app->getModule('dashboard')->layouts;
    }

    /**
     * @return array
     */
    public static function getLayoutsTitles()
    {
        $titles = [];
        foreach(self::getLayouts() as $key => $value) {
            $titles[$key] = $value['title'];
        }
        return $titles;
    }

    /**
     * @return string
     */
    public function getLayoutTitle()
    {
        if ($this->layout && isset(self::getLayouts()[$this->layout])) {
            return self::getLayouts()[$this->layout]['title'];
        }
        return 'Не определен';
    }

    /**
     * @return bool
     */
    public function isTypeTemplate()
    {
        return $this->isType(self::TYPE_TEMPLATE);
    }

    /**
     * @return bool
     */
    public function isTypeUser()
    {
        return $this->isType(self::TYPE_USER);
    }

    /**
     * @param int $priority
     * @return bool
     */
    public function isType($type)
    {
        if ($type === null || $this->type === null) {
            return false;
        }
        return $this->type == $type;
    }

    /**
     * @return array
     */
    public function rules() // TODO реализовать правила
    {
        return [
          // Create
            [ ['key'], 'required', 'on' => 'create' ],
            [ ['key'], 'unique', 'targetClass' => static::className(), 'targetAttribute' => 'key', 'filter' => [
                'is_deleted' => self::IS_DELETE_FALSE,
            ], 'message' => 'Рабочий стол с таким ключом уже существует', 'on' => 'create' ],
            [ ['title'], 'required', 'on' => 'create' ],
            [ ['description'], 'string', 'on' => 'create' ],
            [ ['layout'], 'string', 'on' => 'create' ],
            [ ['type'], 'integer', 'on' => 'create' ],
//            [ ['owner_id'], 'integer', 'on' => 'create' ], // todo: Replace like in SchemaBuilderTrait
          // Update
            [ ['key'], 'required', 'on' => 'update' ],
            [ ['title'], 'required', 'on' => 'update' ],
            [ ['description'], 'string', 'on' => 'update' ],
            [ ['layout'], 'string', 'on' => 'update' ],
            [ ['type'], 'integer', 'on' => 'update' ],
//            [ ['owner_id'], 'integer', 'on' => 'update' ], // todo: Replace like in SchemaBuilderTrait
        ];
    }

    /**
     * @return array
     */
    public function attributeLabelsOverride()
    {
        return [
            'modelTitle' => 'Рабочий стол',
            'key' => 'Ключ',
            'title' => 'Наименование',
            'titleWithLink' => 'Наименование',
            'description' => 'Описание',
            'layout' => 'Схема',
            'type' => 'Тип',
            'typeLabel' => 'Тип',
            'owner_id' => 'Владелец',
            'owner' => 'Владелец',
            'ownerName' => 'Владелец',
            'ownerFullName' => 'Владелец',
        ];
    }
}
