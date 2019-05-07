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

    public function saveSettings($form)
    {
        foreach($form->attributes() as $attribute) {
            $conf = Config::find()
                ->notDeleted()
                ->where(['key' => $attribute])
                ->one();
            if (!$conf) {
                continue;
            }
            $conf->value = $form->$attribute;
            $conf->save();
        }
    }
}
