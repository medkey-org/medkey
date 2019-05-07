<?php
namespace app\modules\config\widgets\form;

use app\common\widgets\FormWidget;
use app\common\wrappers\DynamicModal;
use app\modules\config\ConfigModule;
use app\modules\config\models\orm\Config;

class SettingForm extends FormWidget
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->action = Url::to([$this->directory['config']['updateForm']['action'], 'id' => $this->model->id]);
        $this->validationUrl = Url::to([$this->directory['config']['updateForm']['validationUrl'], 'id' => $this->model->id]);
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function renderForm($model, $form)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function wrapperOptions()
    {
        return [
            'wrapperClass' => DynamicModal::className(),
            'header' => ConfigModule::t('common', 'Setting'),
        ];
    }
}
