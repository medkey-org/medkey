<?php
namespace app\common\widgets;

use app\common\base\Module;

/**
 * Class WidgetRegistryService
 * @package Common\Widgets
 * @copyright 2012-2019 Medkey
 *
 */
class WidgetRegistry
{
    public static function getRegistry($module)
    {
        $classes = [];
        /** @var Module $m */
        $m = \Yii::$app->getModule($module);
        $dir = $m->getWidgetPath();
        $parentModule = $m->getUniqueId();
        $subModules = $m->getModules();
        if (!empty($subModules)) {
            // recursive
            foreach ($subModules as $subModule => $val) {
                $classes[] = static::registry($parentModule . '/' . $subModule);
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
            $classes[] = $m->getWidgetNamespace() . '\\' . $basename;
        }
        return $classes;
    }
}
