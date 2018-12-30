<?php
namespace app\modules\config\widgets\grid;

use app\common\button\WidgetLoaderButton;
use app\common\db\BaseFinder;
use app\common\grid\GridView;
use app\common\helpers\Html;
use app\common\wrappers\Block;
use app\modules\config\ConfigModule;
use app\modules\config\entities\DirectoryEntity;
use app\modules\config\widgets\form\DirectoryCreateForm;
use app\modules\config\widgets\form\DirectoryUpdateForm;
use yii\base\InvalidValueException;

/**
 * Directory record list widget class
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class DirectoryEntityGrid extends GridView
{
    /**
     * @var bool
     */
    public $wrapper = true;
    /**
     * @var array
     */
    public $config;
    /**
     * @var string
     */
    public $key;
    /**
     * @var DirectoryEntity
     */
    private $entity;


    /**
     * DirectoryEntityGrid constructor.
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
        $this->config = $this->entity->findDirectory($this->key);
        if (!is_array($this->config) || !isset($this->config['config']) || !isset($this->config['config']['grid']) || !isset($this->config['config']['grid']['finderClass']) || !isset($this->config['label'])) {
            throw new InvalidValueException(ConfigModule::t('common', 'params in directoryRecordGrid is not defined')); // todo normalize text
        }
        /** @var BaseFinder $filterModel */
        $filterModel = $this->config['config']['grid']['finderClass'];
        if (!class_exists($filterModel)) {
            throw new InvalidValueException("Finder `{$filterModel}` is not found");
        }
        $this->filterModel = $filterModel::ensure($this->filterModel, 'search');
        if (!empty($this->config['config']) && is_array($this->config['config']['grid'])) {
            $this->columns = $this->config['config']['grid']['columns'];
        }
        if (!empty($this->config['config']['createForm'])) {
            $this->actionButtons['create'] = [
                'class' => WidgetLoaderButton::className(),
                'widgetClass' => DirectoryCreateForm::className(),
                'widgetConfig' => [
                    'key' => $this->key,
                ],
                'isDynamicModel' => false,
                'disabled' => false,
                'value' => '',
                'options' => [
                    'class' => 'btn btn-primary btn-xs',
                    'icon' => 'plus'
                ]
            ];
        }
        if (!empty($this->config['config']['updateForm'])) {
            $this->actionButtons['update'] = [
                'class' => WidgetLoaderButton::className(),
                'widgetClass' => DirectoryUpdateForm::className(),
                'widgetConfig' => [
                    'key' => $this->key,
                ],
                'disabled' => true,
                'value' => '',
                'options' => [
                    'class' => 'btn btn-primary btn-xs',
                    'icon' => 'edit'
                ]
            ];
        }
        parent::init();
    }

    /**
     * @return array
     */
    public function wrapperOptions()
    {
        return [
            'wrapperClass' => Block::className(),
            'header' => ConfigModule::t('common', Html::encode($this->config['label']))
        ];
    }
}
