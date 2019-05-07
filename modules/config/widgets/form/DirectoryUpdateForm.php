<?php
namespace app\modules\config\widgets\form;

use app\common\db\ActiveRecord;
use app\common\widgets\FormWidget;
use app\common\wrappers\DynamicModal;
use app\modules\config\ConfigModule;
use app\modules\config\entities\DirectoryEntity;
use yii\helpers\Url;
use app\common\widgets\DynamicFormWidget;

/**
 * Редактирование значения справочника
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class DirectoryUpdateForm extends FormWidget
{
    /**
     * @inheritdoc
     */
    public $method = 'post';
    /**
     * @var string
     */
    public $key;
    /**
     * @var \app\modules\config\entities\DirectoryEntity
     */
    private $entity;
    /**
     * @var array
     */
    private $directory;

    /**
     * DirectoryUpdateForm constructor.
     * @param DirectoryEntity $entity
     * @param array $config
     */
    public function __construct(DirectoryEntity $entity, array $config = [])
    {
        $this->entity = $entity;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->directory = $this->entity->findDirectory($this->key);
        $modelClass = $this->directory['config']['updateForm']['ormClass'];
        $this->model = $modelClass::ensureWeak($this->model, ActiveRecord::SCENARIO_UPDATE);
        $this->action = Url::to([$this->directory['config']['updateForm']['action'], 'id' => $this->model->id]);
        $this->validationUrl = Url::to([$this->directory['config']['updateForm']['validationUrl'], 'id' => $this->model->id]);
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function renderForm($model, $form)
    {
        echo DynamicFormWidget::widget([
            'attributes' => $this->directory['config']['updateForm']['attributes'],
            'model' => $this->model,
            'form' => $form,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function wrapperOptions()
    {
        return [
            'wrapperClass' => DynamicModal::className(),
            'header' => ConfigModule::t('common', 'Update record'),
        ];
    }
}
