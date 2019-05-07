<?php
namespace app\modules\config\application;

use app\common\service\ApplicationService;
use app\common\service\exception\AccessApplicationServiceException;
use app\modules\config\ConfigModule;
use app\modules\config\models\orm\Config;

class ConfigService extends ApplicationService implements ConfigServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getAllSettings()
    {
        if (!$this->isAllowed('getAllSettings')) {
            throw new AccessApplicationServiceException(ConfigModule::t('common', 'Access to the setting list restricted'));
        }
        return Config::find()
            ->notDeleted()
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getApplicationTitle()
    {
        $conf = Config::find()
            ->notDeleted()
            ->where(['key' => 'application_title'])
            ->one();
        if ($conf) {
            return $conf->value;
        }
        return getenv('DEFAULT_APP_TITLE');
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function getPrivileges()
    {
        return [
            'getAllSettings' => ConfigModule::t('common','Get All Settings')
        ];
    }

    public function aclAlias()
    {
        return ConfigModule::t('common', 'Настройки');
    }
}
