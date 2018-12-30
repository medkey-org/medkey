<?php
namespace app\modules\config\application;

use yii\data\DataProviderInterface;

/**
 * Class DirectoryServiceInterface
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
interface DirectoryServiceInterface
{
    /**
     * @return DataProviderInterface
     */
    public function getDirectoryList();
}
