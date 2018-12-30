<?php
namespace app\common\workflow;

use app\common\base\Module;

/**
 * Class ServiceRegistry
 * @package Common\Workflow
 * @copyright 2012-2019 Medkey
 *
 */
class WorkflowRegistry
{
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
        $dir = $m->getWorkflowPath();
        $ns = $m->getWorkflowNamespace();
        $parentModule = $m->getUniqueId();
        $subModules = $m->getModules();
        if (!empty($subModules)) {
            // recursive
            foreach ($subModules as $subModule => $val) {
                $classes = array_merge($classes, WorkflowRegistry::registry($parentModule . '/' . $subModule, $withNs));
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
     * @throws \Exception
     */
    public static function getNamespace($module, $className)
    {
        if (class_exists($className)) {
            return $className;
        }
        /** @var Module $m */
        $m = \Yii::$app->getModule($module);
        if ($m === null) {
            throw new \Exception('Module not found.');
        }
        $parentModule = $m->getUniqueId();
        $subModules = $m->getModules();
        if (!empty($subModules)) {
            // recursive
            foreach ($subModules as $subModule => $val) {
                $modelClass = WorkflowRegistry::getNamespace($parentModule . '/' . $subModule, $className);
                if (!class_exists($modelClass)) {
                    continue;
                }
                return $modelClass;
            }
        }
        $workflowNamespace = $m->getWorkflowNamespace();
        $modelClass = $workflowNamespace . '\\' . $className;
        if (!class_exists($modelClass)) {
            return null;
        }
        return $modelClass;
    }
}
