<?php
namespace app\modules\config\port\rest\controllers;

use app\common\web\Controller;
use app\modules\config\application\ConfigServiceInterface;
use yii\base\DynamicModel;

class SettingController extends Controller
{
    /**
     * @var ConfigServiceInterface
     */
    public $configService;

    public function __construct($id, $module, ConfigServiceInterface $configService, array $config = [])
    {
        $this->configService = $configService;
        parent::__construct($id, $module, $config);
    }

    public function actionSave()
    {
        $data = \Yii::$app->request->getBodyParams();

        $configs = $this->configService->getAllSettings();
        $attributes = [];
        foreach ($configs as $config) {
            $attributes[$config->key] = $config->value;
        }

        $form = new DynamicModel($attributes);
        $form->addRule('language', 'string'); // todo автоматизировать
        $form->addRule('application_title', 'string');
        $form->load($data);

        $this->configService->saveSettings($form);
    }
}
