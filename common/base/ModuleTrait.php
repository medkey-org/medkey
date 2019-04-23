<?php
namespace app\common\base;

use app\common\helpers\ClassHelper;
use app\common\i18n\PhpMessageSource;
use yii\base\InvalidConfigException;
use Yii;
use yii\helpers\FileHelper;

/**
 * Class ModuleTrait
 * @package Common\Base
 * @copyright 2012-2019 Medkey
 */
trait ModuleTrait
{
    /**
     * @var string
     */
    public $aliasId;
    /**
     * Enable dynamic modules in application
     * @var bool
     */
    public $dynamicModule = false;
    /**
     * Enabled dynamic DI's map for each module
     * @var bool
     */
    public $dynamicModuleDI = true;
    /**
     * @var string
     */
    private $_widgetNamespace;
    /**
     * @var string
     */
    private $_ormNamespace;
    /**
     * @var string
     */
    private $_workflowNamespace;
    /**
     * @var string
     */
    private $_applicationNamespace;
    /**
     * @var string
     */
    private $_workflowHandlerNamespace;
    /**
     * @var string
     */
    private $_widgetPath;
    /**
     * @var string
     */
    private $_ormPath;
    /**
     * @var string
     */
    private $_workflowPath;
    /**
     * @var string
     */
    private $_applicationPath;
    /**
     * @var string
     */
    private $_workflowHandlerPath;
    /**
     * @var array
     */
    static $i18n = [];

    /**
     * @inheritdoc
     */
    public function createControllerByID($id)
    {
        if (PHP_SAPI == 'cli') {
            return parent::createControllerByID($id);
        }
        $pos = strrpos($id, '/');
        if ($pos === false) {
            $prefix = '';
            $className = $id;
        } else {
            $prefix = substr($id, 0, $pos + 1);
            $className = substr($id, $pos + 1);
        }

        if (!preg_match('%^[a-z][a-z0-9\\-_]*$%', $className)) {
            return null;
        }
        if ($prefix !== '' && !preg_match('%^[a-z0-9_/]+$%i', $prefix)) {
            return null;
        }
        if ($prefix !== '') {
            $prefix .= 'controllers\\';
        }
        $className = str_replace(' ', '', ucwords(str_replace('-', ' ', $className))) . 'Controller';
        $className = ltrim($this->controllerNamespace . '\\' . str_replace('/', '\\', $prefix) . $className, '\\');
        if (strpos($className, '-') !== false || !class_exists($className)) {
            return null;
        }
        if (is_subclass_of($className, 'yii\base\Controller')) {
            $controller = Yii::createObject($className, [$id, $this]);
            return get_class($controller) === $className ? $controller : null;
        } elseif (YII_DEBUG) {
            throw new InvalidConfigException("Controller class must extend from \\yii\\base\\Controller.");
        }
        return null;
    }

    /**
     * @return string
     */
    public function getWidgetNamespace()
    {
        return $this->_widgetNamespace;
    }

    /**
     * @return string
     */
    public function getWorkflowHandlerNamespace()
    {
        return $this->_workflowHandlerNamespace;
    }

    /**
     * @return string
     */
    public function getApplicationNamespace()
    {
        return $this->_applicationNamespace;
    }

    /**
     * @return string
     */
    public function getOrmNamespace()
    {
        return $this->_ormNamespace;
    }

    /**
     * @return string
     */
    public function getWorkflowNamespace()
    {
        return $this->_workflowNamespace;
    }

    /**
     * @return string
     */
    public function getWorkflowHandlerPath()
    {
        if ($this->_workflowHandlerPath === null) {
            $this->_workflowHandlerPath = $this->getBasePath() . DIRECTORY_SEPARATOR . 'handlers';
        }
        return $this->_workflowHandlerPath;
    }

    /**
     * @return string
     */
    public function getOrmPath()
    {
        if ($this->_ormPath === null) {
            $this->_ormPath = $this->getBasePath() . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'orm';
        }
        return $this->_ormPath;
    }

    /**
     * @return string
     */
    public function getApplicationPath()
    {
        if ($this->_applicationPath === null) {
            $this->_applicationPath = $this->getBasePath() . DIRECTORY_SEPARATOR . 'application';
        }
        return $this->_applicationPath;
    }

    /**
     * @return string
     */
    public function getWidgetPath()
    {
        if ($this->_widgetPath === null) {
            $this->_widgetPath = $this->getBasePath() . DIRECTORY_SEPARATOR . 'widgets';
        }
        return $this->_widgetPath;
    }

    /**
     * @return string
     */
    public function getWorkflowPath()
    {
        if ($this->_workflowPath === null) {
            $this->_workflowPath = $this->getBasePath() . DIRECTORY_SEPARATOR . 'workflow';
        }
        return $this->_workflowPath;
    }

    /**
     * @return void
     */
    public static function registerTranslations()
    {
        $shortName = mb_strtolower(ClassHelper::getShortName(get_called_class()));
        array_push(static::$i18n, mb_strtolower($shortName)); // todo PATH, т.к. имена подмодулей могут совпадать
        $name = str_replace('module', '', $shortName);
        $path = self::getModulePath($name);

        // Getting module translations list
        $translationList = array_merge(static::translationList(), [
            'common', 'error',
        ]);

        $fileMap = [];
        foreach ($translationList as $value) {
            $fileMap[$path . '/' . $name . '/' . $value] = $value . '.php';
        }

        \Yii::$app->i18n->translations[$path . '/' . $name . '/*'] = [
            'class' => PhpMessageSource::className(),
            'basePath' => '@app/' . $path . '/' . $name . '/messages',
            'sourceLanguage' => 'en-US',
            'fileMap' => $fileMap,
        ];
    }

    /**
     * Method can contain custom module translation fileNames, for ex.:
     * return ['patient', 'ehr', 'attendance'];
     * @return array
     */
    public static function translationList()
    {
        return [];
    }

    /**
     * @param string $category
     * @param string $message
     * @param array $params
     * @param string $language
     * @return string
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        $shortName = mb_strtolower(ClassHelper::getShortName(get_called_class()));
        $name = str_replace('module', '', $shortName);
        $path = self::getModulePath($name);
        if (!in_array($shortName, static::$i18n)) {
            self::registerTranslations();
        }
        return \Yii::t($path . '/' . $name . '/' . $category, $message, $params, $language);
    }

    /**
     * @param string $name
     * @return string
     */
    public static function getModulePath($name)
    {
        $path = ClassHelper::getNamespace(get_called_class());
        $path = str_replace('app\\', '', $path);
        $path = str_replace('\\' . $name, '', $path);
        $path = str_replace('\\', '/', $path);
        return $path;
    }

    public function setDynamicModules($dynamicDI)
    {
        $path = \Yii::$app->getBasePath() . DIRECTORY_SEPARATOR . 'modules';
        $modules = array_map(function ($m) {
            return basename($m);
        }, FileHelper::findDirectories($path, [
            'recursive' => false,
        ])
        ); // no support submodules
        foreach ($modules as $m) {
            // @todo возможно вынести NS app в отдельный конфиг
            $class = 'app\modules\\' . $m . '\\' . ucfirst($m) . 'Module';
            $bootstrapClass = 'app\modules\\' . $m . '\\' . 'Bootstrap';
            $obj = \Yii::createObject($class, [
                $m,
            ]);
            $this->setModule($m, [
                'class' => $class,
                'aliasId' => $obj->aliasId
            ]);
            if ($dynamicDI && class_exists($bootstrapClass)) {
                $this->bootstrap[] = 'app\modules\\' . $m . '\\' . 'Bootstrap';
            }
            unset($obj); // todo удаляем из памяти, но если регистрация синглтон, то не нужно. Протестировать
        }
    }

    public function getAllModules()
    {
        $modules = \Yii::$app->getModules();
        $aliases = [];
        if (empty($modules)) {
            return $aliases;
        }
        foreach ($modules as $id => $module) {
            if ($module instanceof Module && property_exists($module, 'aliasId') && !empty($module->aliasId)) {
                $aliases[] = $module->getUniqueId();
            } elseif (is_array($module) && !empty($module['aliasId'])) {
                $m = \Yii::$app->getModule($id);
                $aliases[] = $m->getUniqueId();
            } elseif ($module instanceof Module) {
                $aliases[] = $module->getUniqueId();
            } else {
                $m = \Yii::$app->getModule($id);
                $aliases[] = $m->getUniqueId();
            }
        }
        return $aliases;
    }

    /**
     * @param array $except
     * @return array
     */
    public function getModuleAliases($except = [])
    {
        $modules = \Yii::$app->getModules();
        foreach ($modules as $id => $m) {
            if (in_array($id, $except)) {
                unset($modules[$id]);
            }
        }
        $aliases = [];
        if (empty($modules)) {
            return $aliases;
        }
        foreach ($modules as $id => $module) {
            if ($module instanceof Module && property_exists($module, 'aliasId') && !empty($module->aliasId)) {
                $aliases[$module->getUniqueId()] = $module->aliasId;
            } elseif (is_array($module) && !empty($module['aliasId'])) {
                $m = \Yii::$app->getModule($id);
                $aliases[$m->getUniqueId()] = $module['aliasId'];
            } elseif ($module instanceof Module) {
                $aliases[$module->getUniqueId()] = $module->getUniqueId();
            } else {
                $m = \Yii::$app->getModule($id);
                $aliases[$m->getUniqueId()] = $m->getUniqueId();
            }
        }
        return $aliases;
    }
}
