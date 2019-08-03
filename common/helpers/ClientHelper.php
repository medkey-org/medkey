<?php
namespace app\common\helpers;

/**
 * Class ClientHelper
 * @package Common\Helpers
 * @copyright 2012-2019 Medkey
 */
class ClientHelper
{
    /**
     * @var int
     */
    const REMOVE_DELAY = 7000;


    /**
     * @return string
     */
    public static function getJsonParameters()
    {
        $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

        return Json::encode(static::getParameters(), $options);
    }

    /**
     * @return array
     */
    public static function getParameters()
    {
        return [
            // todo window.onerror - global errors handler
            'module' => Html::encode(\Yii::$app->controller->module->id), // TODO SUBMODULE
            'baseUrl' => \Yii::$app->request->baseUrl,
            'removeDelay' => self::REMOVE_DELAY,
            'widgetLoader' => getenv('WIDGET_LOADER_URL'),
            'login' => Html::encode(\Yii::$app->user->isGuest ? null : \Yii::$app->user->identity->login),
        ];
    }

    /**
     * @param string $message
     * @param int $type
     * @param int $removeDelay
     * @return string
     */
    public static function messageFactory($message, $type, $removeDelay = self::REMOVE_DELAY)
    {
        $arr = [
            'message' => $message,
            'type' => $type,
            'removeDelay' => $removeDelay,
        ];
        $json = Json::encode($arr, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);

        return $json;
    }
}
