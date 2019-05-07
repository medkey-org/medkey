<?php
namespace app\modules\config\widgets\form;

use app\common\helpers\Html;
use app\common\helpers\Url;
use app\common\widgets\FormWidget;
use app\common\wrappers\Block;
use app\modules\config\application\ConfigServiceInterface;
use app\modules\config\ConfigModule;
use app\modules\config\models\orm\Config;
use yii\base\DynamicModel;

class SettingForm extends FormWidget
{
    /**
     * @var ConfigServiceInterface
     */
    public $configService;
    public $afterRedirect = true;
    public $redirectUrl = '';
    public $wrapper = true;
    /**
     * @var DynamicModel
     */
    protected $model;

    /**
     * SettingForm constructor.
     * @param ConfigServiceInterface $configService
     * @param array $config
     */
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
        $this->action = Url::to(['/config/rest/setting/save']);
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm($model, $form)
    {
        $attributes = $this->model->attributes();
        foreach ($attributes as $attribute) {
            if ($attribute === 'language') {
                echo $form
                    ->field($model, $attribute)
                    ->select2(Config::listLanguage(), [], [
                        'allowClear' => false,
                    ])
                    ->label(ConfigModule::t('common', 'Language'));
            }
            if ($attribute === 'application_title') {
                echo $form
                    ->field($model, $attribute)
                    ->label(ConfigModule::t('common', 'Application title'));
            }
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
            'wrapperClass' => Block::class,
            'header' => ConfigModule::t('common', 'Settings'),
        ];
    }
}
