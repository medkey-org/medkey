<?php
namespace app\modules\config\widgets\form;

use app\common\helpers\Html;
use app\common\widgets\FormWidget;
use app\common\wrappers\DynamicModal;
use app\modules\config\application\ConfigServiceInterface;
use app\modules\config\ConfigModule;
use app\modules\config\models\orm\Config;
use yii\base\DynamicModel;

class SettingForm extends FormWidget
{
    public $configService;

    /**
     * @var DynamicModel
     */
    protected $model;

    public function __construct(ConfigServiceInterface $configService, array $config = [])
    {
        $this->configService = $configService;
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $configs = $this->configService->getAllSettings();
        $attributes = [];
        foreach ($configs as $config) {
            $attributes[$config->key] = $config->value;
        }
        $this->model = new DynamicModel($attributes);
//        $this->action = Url::to(['', 'id' => $this->model->id]);
//        $this->validationUrl = Url::to(['', 'id' => $this->model->id]);
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm($model, $form)
    {
        $attributes = $this->model->attributes();
        foreach ($attributes as $attribute) {
            echo $form
                ->field($model, $attribute)
                ->select2(Config::listLanguage(), [], [
                    'allowClear' => false,
                ]);
        }
        echo Html::submitButton(ConfigModule::t('common', 'Save'), [
            'class' => 'btn btn-primary',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function wrapperOptions()
    {
        return [
            'wrapperClass' => DynamicModal::class,
            'header' => ConfigModule::t('common', 'Setting'),
        ];
    }
}
