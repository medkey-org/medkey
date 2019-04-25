<?php
namespace app\modules\config\widgets\form;

use app\modules\config\ConfigModule;
use yii\helpers\Url;
use app\common\db\ActiveRecord;
use app\common\widgets\FormWidget;
use app\common\wrappers\DynamicModal;
use app\modules\config\entities\DirectoryEntity;
use app\common\widgets\DynamicFormWidget;

/**
 * Добавление значения в справочник
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class DirectoryCreateForm extends FormWidget
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
     * DirectoryCreateForm constructor.
     * @param \app\modules\config\entities\DirectoryEntity $entity
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
        /** @var ActiveRecord $modelClass */
        $modelClass = $this->directory['config']['createForm']['ormClass'];
        $this->model = $modelClass::ensureWeak($this->model, ActiveRecord::SCENARIO_CREATE);
        $this->action = Url::to([$this->directory['config']['createForm']['action']]);
        $this->validationUrl = Url::to([$this->directory['config']['createForm']['validationUrl']]);
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function renderForm($model, $form)
    {
        echo DynamicFormWidget::widget([
            'attributes' => $this->directory['config']['createForm']['attributes'],
            'model' => $this->model,
            'form' => $form
        ]);
    }

    /**
     * @inheritdoc
     */
    public function wrapperOptions()
    {
        return [
            'wrapperClass' => DynamicModal::className(),
            'header' => ConfigModule::t('directory', 'Create record'),
        ];
    }
}
