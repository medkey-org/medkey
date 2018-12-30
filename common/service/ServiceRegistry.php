<?php
namespace app\common\service;

use app\common\base\Module;
use app\common\service\exception\ApplicationServiceException;
use yii\base\BaseObject;

/**
 * Class ServiceRegistry
 * @package Common\Service
 * @copyright 2012-2019 Medkey
 */
class ServiceRegistry extends BaseObject
{
    /**
     * @param $module
     * @param $interface
     * @return null|string
     */
    public function factoryService($module, $interface)
    {
        if (interface_exists($interface)) {
            return $interface;
        }
        /** @var Module $m */
        $m = \Yii::$app->getModule($module);
        if ($m === null) {
            throw new ApplicationServiceException('Module not found.');
        }
        $parentModule = $m->getUniqueId();
        $subModules = $m->getModules();
        if (!empty($subModules)) {
            // recursive
            foreach ($subModules as $subModule => $val) {
                $modelClass = $this->factoryService($parentModule . '/' . $subModule, $interface);
                if (!interface_exists($modelClass)) {
                    continue;
                }
                return $modelClass;
            }
        }
        $serviceNamespace = $m->getApplicationNamespace();
        $modelClass = $serviceNamespace . '\\' . $interface;
        if (!interface_exists($modelClass)) {
            return null;
        }
        return $modelClass;
    }

    /**
     * @param $module
     * @param bool $withNs
     * @return array
     * @throws \ReflectionException
     */
    public static function registry($module, $withNs = false)
    {
        $classes = [];
        /** @var Module $m */
        $m = \Yii::$app->getModule($module);
        if ($m === null) {
            return $classes;
        }
        $dir = $m->getApplicationPath();
        $ns = $m->getApplicationNamespace();
        $parentModule = $m->getUniqueId();
        $subModules = $m->getModules();
        if (!empty($subModules)) {
            // recursive
            foreach ($subModules as $subModule => $val) {
                $classes = array_merge($classes, ServiceRegistry::registry($parentModule . '/' . $subModule, $withNs));
            }
        }
        if (!file_exists($dir)) {
            return $classes;
        }
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || !is_file($dir . DIRECTORY_SEPARATOR . $file)) {
                continue;
            }
            $f = new \SplFileInfo($dir . DIRECTORY_SEPARATOR . $file);
            if ($f->getExtension() !== 'php') { // php 5.3.6+
                continue;
            }
            $basename = $f->getBasename('.php');
            $class = $ns . '\\' . $basename;
            if ((new \ReflectionClass($class))->isInterface()) {
                continue;
            }
            $withNs ? $classes[] = $class : $classes[] = $basename;
        }
        return $classes;
    }

    /**
     * @param $module
     * @param $className
     * @return null|string
     */
    public static function getNamespace($module, $className)
    {
        if (class_exists($className)) {
            return $className;
        }
        /** @var Module $m */
        $m = \Yii::$app->getModule($module);
        if ($m === null) {
            throw new ApplicationServiceException('Module not found.');
        }
        $parentModule = $m->getUniqueId();
        $subModules = $m->getModules();
        if (!empty($subModules)) {
            // recursive
            foreach ($subModules as $subModule => $val) {
                $modelClass = ServiceRegistry::getNamespace($parentModule . '/' . $subModule, $className);
                if (!class_exists($modelClass)) {
                    continue;
                }
                return $modelClass;
            }
        }
        $serviceNamespace = $m->getApplicationNamespace();
        $modelClass = $serviceNamespace . '\\' . $className;
        if (!class_exists($modelClass)) {
            return null;
        }
        return $modelClass;
    }

    /**
     * @param $module
     * @param $className
     * @return array
     * @throws \ReflectionException
     */
    public static function getPublicMethods($module, $className)
    {
        /** @var Module $m */
        $m = \Yii::$app->getModule($module);
        if ($m === null) {
            throw new ApplicationServiceException('Module not found.');
        }
        $serviceNamespace = $m->getApplicationNamespace();
        $modelClass = $serviceNamespace . '\\' . $className;
        if (!class_exists($modelClass)) {
            throw new ApplicationServiceException('Service class not found.');
        }
        $r = (new \ReflectionClass($modelClass))->getMethods();
        $methods = [];
        foreach ($r as $method) {
            if (!$method->isPublic() || $method->class !== $modelClass) {
                continue;
            }
            $methods[] = $method->getName();
        }
        return $methods;
    }
}
