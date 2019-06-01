<?php
namespace app\modules\config\widgets\grid;

use app\common\db\ActiveRecord;
use app\common\grid\GridView;
use app\common\helpers\Html;
use app\common\helpers\Url;
use app\modules\config\application\DocumentServiceInterface;
use app\common\wrappers\Block;
use app\modules\config\ConfigModule;

/**
 * Directory list widget class
 * @package Module\Config
 * @copyright 2012-2019 Medkey
 */
class DocumentTemplateGrid extends GridView
{
    /**
     * @var bool
     */
    public $wrapper = true;
    /**
     * @var DocumentServiceInterface
     */
    private $documentService;

    /**
     * DirectoryGrid constructor.
     * @param DocumentServiceInterface $documentService
     * @param array $config
     */
    public function __construct(DocumentServiceInterface $documentService, array $config = [])
    {
        $this->documentService = $documentService;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->dataProvider = $this->documentService->getTemplateList();
        $this->columns = [
            [
                'attribute' => 'label',
                'label' => ConfigModule::t('document', 'Template name'),
                'value' => function ($model) {
                    return Html::a(Html::encode($model['label']), Url::to([
                        '/config/ui/directory/view',
                        'id' => Html::encode($model['key']),
                    ]));
                },
                'format' => 'raw',
            ],
            [
                'header' => ConfigModule::t('document', 'Count values'),
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
            'header' => ConfigModule::t('document', 'Document templates')
        ];
    }
}
