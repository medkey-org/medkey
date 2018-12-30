<?php
namespace app\common\db;

use app\common\base\Module;
use yii\base\InvalidValueException;

/**
 * Class ActiveRecordRegistry
 * @package Common\DB
 * @copyright 2012-2019 Medkey
 */
class ActiveRecordRegistry
{
    /**
     * @param string $module uniqueId of module
     * @param string $className
     * @return string
     */
    public static function getNamespace($module, $className)
    {
        /** @var Module $m */
        $m = \Yii::$app->getModule($module);
        if ($m === null) {
            throw new InvalidValueException('Модуль не найден.');
        }
        $ormNamespace = $m->getOrmNamespace();
        $modelClass = $ormNamespace . '\\' . $className;
        if (class_exists($modelClass)) {
            return $modelClass;
        }
        $modelClass = 'app\common\logic\orm\\' . $className;
        if (!class_exists($modelClass)) {
            throw new \DomainException('Не удалось найти ORM ' . $modelClass);
        }
        return $modelClass;
    }

    /**
     * @param string $module
     * @return array
     */
    public static function registry($module = null)
    {
        $classes = [];
        /** @var Module $m */
        $m = \Yii::$app->getModule($module);
        if ($m === null) {
            return $classes;
        }
        $dir = $m->getOrmPath();
        $parentModule = $m->getUniqueId();
        $subModules = $m->getModules();
        if (!empty($subModules)) {
            // recursive
            foreach ($subModules as $subModule => $val) {
                $classes = array_merge($classes, ActiveRecordRegistry::registry($parentModule . '/' . $subModule));
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
            $classes[$basename] = $basename;
        }
        return $classes;
    }
}
