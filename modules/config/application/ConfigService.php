<?php
namespace app\modules\config\application;

use app\common\service\ApplicationService;
use app\modules\config\models\orm\Config;

class ConfigService extends ApplicationService implements ConfigServiceInterface
{
    public function getAllSettings()
    {
        return Config::find()->notDeleted()->all();
    }
}
