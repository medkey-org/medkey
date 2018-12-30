<?php
namespace app\modules\config\entities;

use Symfony\Component\Config\FileLocator;

/**
 * Class DirectoryEntity
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class DirectoryEntity
{
    /**
     * @param string $key
     * @return array
     */
    public function findDirectory($key = null)
    {
        $configDirectories =
            \Yii::$app->getBasePath()
            . DIRECTORY_SEPARATOR
            . 'modules'
            . DIRECTORY_SEPARATOR
            . 'config'
            . DIRECTORY_SEPARATOR
            . 'config';

        $fileLocator = new FileLocator($configDirectories);
        $config = $fileLocator->locate('directories.php', null, false); // todo yaml or xml
        $f = include $config[0];
        if (!is_array($f)) {
            return null;
        }
        if (empty($key)) {
            return $f;
        }
        foreach ($f as $row) {
            if (isset($row['key']) && $row['key'] === $key) {
                return $row;
            }
        }
        return null;
    }
}
