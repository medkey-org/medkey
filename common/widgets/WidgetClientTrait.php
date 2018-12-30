<?php
namespace app\common\widgets;

use app\common\helpers\ClassHelper;
use app\common\web\View;
use app\common\helpers\Html;
use app\common\helpers\ArrayHelper;
use yii\base\BaseObject;
use yii\base\InvalidValueException;
use yii\base\InvalidCallException;
use yii\base\Model;
use yii\helpers\Inflector;
use yii\helpers\Json;
use yii\base\Arrayable;

/**
 * Trait WidgetClientTrait
 *
 * @property array $config
 * @property Model $model
 *
 * @package Common\Widgets
 * @copyright 2012-2019 Medkey
 */
trait WidgetClientTrait
{
    /**
     * @var array
     */
    public $clientViewOptions = [];
    /**
     * @var bool
     */
    public $clientView = true;
    /**
     * @var bool
     */
    public $clientParams = true;
    /**
     * @var
     */
    public $clientClassName;
    /**
     * @var string
     */
    public $clientClassNameDefault;
    /**
     * @var bool
     */
    public $clientWrapperContainer = true;
    /**
     * In case of submodules must be: ModuleModule[_***_]{className}
     * @var string
     */
    public $templateClientName = '{module}_{className}';
    /**
     * @var Model
     */
    private $_model;
    /**
     * @var array
     */
    private $_config = [];


    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * @param $model
     */
    public function setModel($model)
    {
        $this->_model = $model;
    }

    /**
     * @param string $id
     * @throws InvalidValueException
     *
     * @return void
     */
    public function registerClient($id)
    {
        Html::addCssClass($this->options, 'b-client-view');
        $serverModule = ClassHelper::getMatchModule($this, true, '');
        $serverClassName = ClassHelper::getShortName($this);
        if (!empty($this->clientClassName)) {
            $clientClassName = $this->clientClassName;
        } else {
            $clientClassName = $this->prepareClientClassName($serverClassName, $serverModule);
        }
        if ($this->clientParams) {
            $clientOptions = $this->prepareClientOptions(Inflector::camel2id($serverClassName), Inflector::camel2id($serverModule, '/'));
        } else {
            $clientOptions = [];
        }
        $clientOptions = Json::encode($clientOptions, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
        $el = '#' . $id;
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidValueException('Json encode error');
        }

        \Yii::$app->getView()->registerJs($this->createClientWidget($clientClassName, $el, $clientOptions), View::POS_END);
    }

    /**
     * @param string $clientClassName
     * @param string $el
     * @param array|string $clientOptions
     * @return string
     */
    public function createClientWidget($clientClassName, $el, $clientOptions)
    {
        $js = 'try { new ' . $clientClassName . '({\'el\': \'' . $el . '\' , \'params\': ' . $clientOptions . '}) } catch (e) {  ';
        if (isset($this->clientClassNameDefault)) {
            $js .= 'new ' . $this->clientClassNameDefault . '({\'el\': \'' . $el . '\' , \'params\': ' . $clientOptions . '})}'; // todo еще один try catch
        } else {
            $js .= 'console.log(e.message); }';
        }

        return $js;
    }

    /**
     * @param string $serverClassName
     * @param string $serverModule
     * @return string
     */
    public function prepareClientClassName($serverClassName, $serverModule)
    {
        if (empty($serverModule)) {
            return $serverClassName;
        }

        return strtr($this->templateClientName, [
            '{module}' => ucfirst($serverModule), '{className}' => $serverClassName,
        ]);
    }

    /**
     * @param string $serverClassName
     * @param string $serverModule
     * @param bool $toString
     *
     * @return array
     */
    public function prepareClientOptions($serverClassName, $serverModule, $toString = true)
    {
        $clientOptions['className'] = $serverClassName;
        if (!empty($serverModule)) {
            $clientOptions['module'] = $serverModule;
        }
        $clientOptions['config'] = $this->getConfig(true); // todo проверять как-то на примитивы или сериализовать объекты при передаче на клиент
        $model = $this->prepareClientModel();
        if (!empty($model)) {
            $clientOptions['config']['model'] = $model;
        }
        if (ArrayHelper::isAssociative($this->clientViewOptions)) {
            $clientOptions = ArrayHelper::merge($clientOptions, $this->clientViewOptions);
        }
        if ($toString) {
            $clientOptions = ArrayHelper::intToStringRecursive($clientOptions);
        }

        return $clientOptions;
    }

    /**
     * @return mixed
     */
    public function prepareClientModel()
    {
        if ($this->model instanceof Arrayable) {
            return $this->model->toArray();
        }

        return null;
    }

    /**
     * @return array
     */
    public function deniedConfig()
    {
        return [];
    }

    /**
     * @param bool $denied
     *
     * @return array
     */
    public function getConfig($denied = false)
    {
        if ($denied) {
            $d = $this->deniedConfig();
            $config = [];
            $i = 0;
            foreach ($this->_config as $key => $c) {
                if (!in_array($key, $d)) {
                    $config[$key] = $c;
                    $i++;
                }
            }

            return $config;
        }

        return $this->_config;
    }

    /**
     * @param array $config
     *
     * @return void
     */
    public function setConfig($config)
    {
        $prepareConfig = [];
        foreach ($config as $key => $c) {
            if ((($this instanceof BaseObject || $this instanceof BaseObject) && $this->hasProperty($key)) || property_exists($this, $key)) { // todo проверять на примитивы, т.к. объект не вставится нормально в клиент
                $prepareConfig[$key] = $c;
            }
        }
        $this->_config = $prepareConfig;
    }

    /**
     * @param string $content
     * @param array $config
     * @param array $options
     * @param bool $wrapper
     * @param bool $isDynamicModel
     * @param string $queryParams
     * @param string $insertBlockId
     *
     * @return string
     */
    public static function loaderButton($content, array $config = [], array $options = ['class' => 'btn btn-primary'], $isDynamicModel = true, $queryParams = null, $wrapper = true, $insertBlockId = null)
    {
        $config['wrapper'] = $wrapper; // todo check na wrapperAble
        return Html::loaderButton($content, $options, [
            'module' => ClassHelper::getMatchModule(static::className(), false, '/'),
            'className' => Inflector::camel2id(ClassHelper::getShortName(static::className())),
            'config' => $config,
        ], $isDynamicModel, $queryParams, $insertBlockId);
    }

    /**
     * @param string $name
     * @param string $content
     * @param array $config
     * @param array $options
     * @param bool $wrapper
     * @param bool $isDynamicModel
     * @param string $queryParams
     * @param string $insertBlockId
     *
     * @return string
     */
    public static function loaderTag($name, $content, array $config = [], array $options = ['href' => '#'], $isDynamicModel = true, $queryParams = null, $wrapper = true, $insertBlockId = null)
    {
        $config['wrapper'] = $wrapper;
        return Html::loaderTag($name, $content, $options, [
            'module' => ClassHelper::getMatchModule(static::className(), false, '/'),
            'className' => Inflector::camel2id(ClassHelper::getShortName(static::className())),
            'config' => $config,
        ], $isDynamicModel, $queryParams, $insertBlockId);
    }

    /**
     * @param array $config
     * @return string
     * @throws \Exception
     */
    public static function widget($config = [])
    {
        ob_start();
        ob_implicit_flush(false);
        try {
            /* @var $widget Widget */
            $config['class'] = get_called_class();
            $widget = \Yii::createObject($config);
            $out = '';
            if ($widget->beforeRun()) {
                $result = $widget->run();
                $out = $widget->afterRun($result);
            }
        } catch (\Exception $e) {
            // close the output buffer opened above if it has not been closed already
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            throw $e;
        }
        $content = ob_get_clean() . $out;
        if ($widget instanceof WidgetClientInterface && $widget->clientWrapperContainer) {
            $content = Html::tag('div', $content, $widget->options);
        }
        if ($widget instanceof WrapperAbleInterface && $widget->wrapper) {
            return WidgetFactory::wrapperContent($content, $widget->wrapperOptions);
        }

        return $content;
    }

    /**
     * @inheritdoc
     */
    public static function begin($config = [])
    {
        ob_start();
        ob_implicit_flush(false);
        $config['class'] = get_called_class();
        /* @var $widget Widget */
        $widget = \Yii::createObject($config);
        static::$stack[] = $widget;

        return $widget;
    }

    /**
     * @inheritdoc
     */
    public static function end()
    {
        if (!empty(static::$stack)) {
            $widget = array_pop(static::$stack);
            if (get_class($widget) === get_called_class()) {
                $out = '';
                if ($widget->beforeRun()) {
                    $content = $widget->run();
                    $out = $widget->afterRun($content);
                }
                $content = ob_get_clean() . $out;
                if ($widget instanceof WidgetClientInterface && $widget->clientWrapperContainer) {
                    $content = Html::tag('div', $content, $widget->options);
                }
                if ($widget instanceof WrapperAbleInterface && $widget->wrapper) {
                    echo WidgetFactory::wrapperContent($content, $widget->wrapperOptions);
                } else {
                    echo $content;
                }
                return $widget;
            } else {
                throw new InvalidCallException('Expecting end() of ' . get_class($widget) . ', found ' . get_called_class());
            }
        } else {
            throw new InvalidCallException('Unexpected ' . get_called_class() . '::end() call. A matching begin() is not found.');
        }
    }
}
