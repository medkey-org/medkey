<?php
namespace app\common\widgets;

use app\common\base\Module;
use app\common\helpers\ClassHelper;
use app\common\wrappers\WrapperInterface;
use yii\base\BaseObject;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * Class WidgetFactory
 * @package Common\Widgets
 * @copyright 2012-2019 Medkey
 *
 */
class WidgetFactory extends BaseObject
{
    const BASIC_MODULE_NAME = '_basic';

    const TYPE_CARD = 'card';
    const TYPE_MISC = 'misc';
    const TYPE_FORM = 'form';
    const TYPE_GRID = 'grid';
    const TYPE_WIZARD = 'wizard';
    const TYPE_SEARCH = 'search';
    const TYPE_HISTORY = 'history';


    /**
     * @param array $params
     * @return string
     * @throws ErrorException
     */
    public static function createWidget($params)
    {
        $moduleName = mb_strtolower($params['module'], 'UTF-8');
        $config = [];
        if ($moduleName !== self::BASIC_MODULE_NAME && !\Yii::$app->hasModule($moduleName)) {
            throw new ErrorException('Module not found.');
        }
        if (isset($params['config']) && ArrayHelper::isAssociative($params['config'])) {
            $config = $params['config'];
        }
        $class = static::getWidget($moduleName, $params['className']);
        // todo проверку на тип класса
        if (!ClassHelper::implementsInterface($class, WrapperAbleInterface::class)) {
            $config['wrapper'] = false;
        }
        if (!class_exists($class)) {
            throw new ErrorException('Widget class not found.');
        }
        /** @var $class Widget $widget */
        $content = $class::widget($config);
        return $content;
    }

    /**
     * @param string $moduleName
     * @param string $className
     * @return string className with namespace
     */
    protected static function getWidget($moduleName, $className)
    {
        if ($moduleName === self::BASIC_MODULE_NAME) {
            return 'app\common\widgets\\' . Inflector::id2camel($className);
        }
        /** @var Module $module */
        $module = \Yii::$app->getModule($moduleName);
        $ns = $module->getWidgetNamespace();
        $pos = strrpos($className, '-');
        $tail = substr($className, $pos + 1);
        switch ($tail) {
            case static::TYPE_CARD:
            case static::TYPE_FORM:
            case static::TYPE_GRID:
            case static::TYPE_HISTORY:
            case static::TYPE_SEARCH:
            case static::TYPE_WIZARD:
                $ns = $ns . '\\' . $tail . '\\';
                break;
            default:
                $ns = $ns . '\\' . static::TYPE_MISC . '\\';
        }
        $className = Inflector::id2camel($className);
        return $ns . $className;
    }

    /**
     * @param string $content
     * @param array $wrapperOptions
     * @return string
     */
    public static function wrapperContent($content, $wrapperOptions)
    {
        if (empty($wrapperOptions['wrapperClass'])) {
            return $content;
        }
        $wrapperClass = $wrapperOptions['wrapperClass'];
        if (!strstr($wrapperClass, 'app\common\wrappers')) {
            $wrapperClass = 'app\common\wrappers\\' . ucfirst(Inflector::id2camel($wrapperClass));
        }

        if (!class_exists($wrapperClass)) {
            return $content;
        }

        if (ClassHelper::implementsInterface($wrapperClass, WrapperInterface::class)) {
            $properties = ClassHelper::getProperties($wrapperClass);
            $config = [];
            $config['wrapperContent'] = $content;
            foreach ($properties as $property) {
                $key = $property->name;
                if (isset($wrapperOptions[$key])) {
                    $config[$key] = $wrapperOptions[$key];
                }
            }
            /** @var Widget $wrapperClass */
            return $wrapperClass::widget($config);
        }
        return $content;
    }
}
