<?php
namespace app\common\helpers;

use yii\base\InvalidValueException;

/**
 * Class ClassHelper
 * @package Common\Helpers
 * @copyright 2012-2019 Medkey
 */
class ClassHelper
{
    /**
     * @param string|object $class
     * @return string
     */
    public static function getShortName($class)
    {
        if (is_object($class)) {
            $f = new \ReflectionClass(get_class($class));
        } elseif (is_string($class)) {
            $f = new \ReflectionClass($class);
        }
        if (isset($f) && $f instanceof \ReflectionClass) {
            return $f->getShortName();
        }

        return null;
    }

    /**
     * @param string|object $class
     *
     * @return null|\ReflectionProperty[]
     */
    public static function getProperties($class)
    {
        if (is_object($class)) {
            $f = new \ReflectionClass(get_class($class));
        } elseif (is_string($class)) {
            $f = new \ReflectionClass($class);
        }
        if (isset($f) && $f instanceof \ReflectionClass) {
            return $f->getProperties(\ReflectionProperty::IS_PUBLIC);
        }

        return null;
    }

    /**
     * @param string|object $class
     * @return string
     */
    public static function getNamespace($class)
    {
        if (is_object($class)) {
            $f = new \ReflectionClass(get_class($class));
        } elseif (is_string($class)) {
            $f = new \ReflectionClass($class);
        }
        if (isset($f) && $f instanceof \ReflectionClass) {
            return $f->getNamespaceName();
        }

        return null;
    }

    /**
     * @param string|object $class
     * @return bool
     */
    public static function inNamespace($class)
    {
        if (is_object($class)) {
            $f = new \ReflectionClass(get_class($class));
        } elseif (is_string($class)) {
            $f = new \ReflectionClass($class);
        }
        if (isset($f) && $f instanceof \ReflectionClass) {
            return $f->inNamespace();
        }

        return null;
    }

    /**
     * @param string $class
     * @param string $interface
     * @return bool
     */
    public static function implementsInterface($class, $interface)
    {
        if (is_string($class)) {
            $f = new \ReflectionClass($class);
            return $f->implementsInterface($interface);
        }

        return false;
    }

    /**
     * @param string|object $class
     * @return string
     */
    public static function getFileName($class)
    {
        $f = new \ReflectionClass(get_class($class));
        $fn = $f->getFileName();

        return dirname($fn);
    }
    
    /**
     * @param object|string $class
     * @param bool $ucfirst
     * @param string $separator
     * @return string
     */
    public static function getMatchModule($class, $ucfirst = true, $separator = '')
    {
        $module = '';
        if (is_object($class)) {
            $class = get_class($class);
        } elseif (!class_exists($class)) {
            throw new InvalidValueException('ClassName is not defined');
        }
        if (!preg_match_all("~(\\\\modules\\\\[a-zA-Z_0-9]+)~", $class, $matches, PREG_PATTERN_ORDER)) {
//            return ucfirst(\Yii::$app->id); // *basic* module
            return null;
        }
        foreach ($matches[1] as $match) {
            if (preg_match_all("~\\\\modules\\\\([a-zA-Z_0-9]+)~", $match, $matchesInto, PREG_PATTERN_ORDER)) {
                if ($ucfirst) {
                    $module .= ucfirst($matchesInto[1][0]);
                } else {
                    $module .= $matchesInto[1][0];
                }
                $module .= $separator;
            }
        }
        if (substr($module, -1) == '/') {
            return substr($module, 0, strlen($module) - 1);
        }

        return $module;
    }

    /**
     * @param string $id module's id
     * @return string
     */
    public static function modulePathToNs($id)
    {
        $modules = explode('/', $id);
        if (empty($modules)) {
            return $id;
        }
        $i = 0;
        $moduleName = '';
        foreach ($modules as $m) {
            if ($i === 0) {
                $moduleName .= $m;
            } else {
                $moduleName .= '\modules\\' . $m;
            }
            $i++;
        }

        return $moduleName;
    }
}
