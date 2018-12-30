<?php
namespace app\common\db;

use Faker\Provider\Base;
use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;

/**
 * Поведение для автоматической подстановки автора и того, кто последним изменил запись
 * @package Common\DB
 * @copyright 2012-2019 Medkey
 */
class AuthorAttributeBehavior extends AttributeBehavior
{
    /**
     * @var int
     */
    public $userCreated = 'user_created_id';
    /**
     * @var int
     */
    public $userUpdated = 'user_updated_id';
    /**
     * @var string
     */
    public $value;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => $this->userCreated,
                BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->userUpdated,
            ];
        }
    }

    /**
     * @inheritdoc
     */
    protected function getValue($event)
    {
        if ($this->value !== null) {
            return parent::getValue($event);
        }
        $eventName = $event->name;
        if ($eventName === BaseActiveRecord::EVENT_BEFORE_UPDATE && isset(\Yii::$app->user)) {
            return !\Yii::$app->user->getId() ? null : \Yii::$app->user->getId();
        } elseif ($eventName === BaseActiveRecord::EVENT_BEFORE_INSERT) {
            $userCreatedAttribute = $this->userCreated;
            if ($event->sender->{$userCreatedAttribute} !== null && !empty($event->sender->{$userCreatedAttribute})) {
                return $event->sender->{$userCreatedAttribute};
            } elseif (isset(\Yii::$app->user)) {
                return !\Yii::$app->user->getId() ? null : \Yii::$app->user->getId();
            }
        }

        return null;
    }
}
