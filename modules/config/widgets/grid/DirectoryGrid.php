<?php
namespace app\modules\config\widgets\grid;

use app\common\db\ActiveRecord;
use app\common\grid\GridView;
use app\common\helpers\Html;
use app\common\helpers\Url;
use app\modules\config\models\finders\DirectoryFinder;
use app\common\wrappers\Block;
use app\modules\config\ConfigModule;
use app\modules\config\application\DirectoryServiceInterface;

/**
 * Directory list widget class
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class DirectoryGrid extends GridView
{
    /**
     * @var bool
     */
    public $wrapper = true;
    /**
     * @var DirectoryServiceInterface
     */
    public $directoryService;


    /**
     * DirectoryGrid constructor.
     * @param DirectoryServiceInterface $directoryService
     * @param array $config
     */
    public function __construct(DirectoryServiceInterface $directoryService, array $config = [])
    {
        $this->directoryService = $directoryService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->filterModel = DirectoryFinder::ensure($this->filterModel, 'search');
        $this->dataProvider = $this->directoryService->getDirectoryList();
        $this->columns = [
            [
                'attribute' => 'label',
                'label' => ConfigModule::t('common', 'Name of directory'),
                'value' => function ($model) {
                    return Html::a(Html::encode($model['label']), Url::to([
                        '/config/ui/directory/view',
                        'id' => Html::encode($model['key']),
                    ]));
                },
                'format' => 'raw',
            ],
            [
                'header' => ConfigModule::t('common', 'Count values'),
                'value' => function ($model) {
                    /** @var ActiveRecord $modelClass */
                    $modelClass = $model['ormClass'];
                    return $modelClass::find()->notDeleted()->count();
                },
                'format' => 'raw',
            ],
        ];
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function wrapperOptions()
    {
        return [
            'wrapperClass' => Block::class,
            'header' => ConfigModule::t('common', 'Directories')
        ];
    }
}
