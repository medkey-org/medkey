<?php
namespace app\modules\dashboard\models\orm;

use app\common\db\ActiveRecord;
use app\common\ddd\EntityInterface;
use app\common\rbac\ClassPermissionableTrait;
use app\common\validators\ForeignKeyValidator;

/**
 * Виджет на рабочем столе
 *
 * @property string     $title
 * @property integer    $dashboard_id
 * @property string     $widget
 * @property integer   $position
 * @property integer   $order
 *
 * @property-read Dashboard $dashboard
 *
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class DashboardItem extends ActiveRecord
{
    use ClassPermissionableTrait;

    /**
     * @var string
     */
    public static $module = 'dashboard';


    /**
     * @return string
     */
    public static function entityName()
    {
        return 'Виджет на рабочем столе'; // todo локализация если необходимо
    }

    /**
     * @return array
     */
    public static function modelIdentity()
    {
        return ['dashboard_id', 'order', 'position'];
    }

    /**
     * Получить модель рабочего стола
     * @return \yii\db\ActiveQuery
     */
    public function getDashboard()
    {
        return $this->hasOne(Dashboard::className(), ['id' => 'dashboard_id']);
    }

    /**
     * @return array
     */
    public static function getDashlets()
    {
        return \Yii::$app->getModule('dashboard')->dashlets;
    }

    /**
     * @return array
     */
    public static function getDashletsTitles()
    {
        $titles = [];
        foreach(self::getDashlets() as $key => $value) {
            $titles[$key] = $value['title'];
        }
        return $titles;
    }

    /**
     * @return string
     */
    public function getDashletTitle()
    {
        if ($this->widget && isset(self::getDashlets()[$this->widget])) {
            return self::getDashlets()[$this->widget]['title'];
        }
        return 'Не определен';
    }

    /**
     * @return array
     */
    public function rules() // TODO реализовать правила
    {
        return [
            // Create
            [ ['dashboard_id'], ForeignKeyValidator::class, ],
            [ ['title', 'dashboard_id', 'widget', 'position', 'order'], 'required', 'on' => 'create' ],
            [ ['position', 'order'], 'integer', 'on' => 'create' ],
            [ ['widget'], 'string', 'on' => 'create' ],
            [ ['position', 'order', 'dashboard_id'],
                'unique',
                'filter' => ['<>', 'is_deleted', 1],
                'when' => function($item) {
                    return is_numeric($item->position) && is_numeric($item->order);
                },
                'targetAttribute' => ['position', 'order', 'dashboard_id'],
                'message' => '{attribute} {value} содержит другой виджет на текущем рабочем столе',
                'on' => 'create', ],
            // Update
            [ ['title', 'position', 'order'], 'required', 'on' => 'update' ],
            [ ['title'], 'string', 'on' => 'update' ],
            [ ['position', 'order'], 'integer', 'on' => 'update' ],
            [ ['position', 'order', 'dashboard_id'],
                'unique',
                'filter' => ['<>', 'is_deleted', 1],
                'when' => function($item) {
                    return is_numeric($item->position) && is_numeric($item->order);
                },
                'targetAttribute' => ['position', 'order', 'dashboard_id'],
                'message' => '{attribute} {value} содержит другой виджет на текущем рабочем столе',
                'on' => 'update', ]
        ];
    }

    /**
     * @return array
     */
    public function attributeLabelsOverride()
    {
        return [
            'modelTitle' => 'Виджет на рабочем столе',
            'title' => 'Наименование',
            'dashboard_id' => 'Рабочий стол',
            'dashboard' => 'Рабочий стол',
            'widget' => 'Виджет',
            'position' => 'Позиция',
            'order' => 'Порядок'
        ];
    }

    /**
     * Возвращает алиас модуля, к которому относится дашлет
     * @return string
     */
    public function getModule()
    {
        $dashlets = \Yii::$app->getModule('dashboard')->dashlets;
        return (isset ($dashlets[$this->widget]['module'])) ? $dashlets[$this->widget]['module'] : 'dashboard';
    }

    /**
     * Возвращает полное имя класса виджета
     * @return string
     */
    public function getWidgetClass()
    {
        return '\app\modules\\' . $this->getModule() . '\dashlets\\' . $this->widget . '\\' . $this->widget;
    }
}
