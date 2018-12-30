<?php
namespace app\common\widgets;

use app\common\base\Model;
use app\common\db\BaseFinder;
use app\common\helpers\ClassHelper;
use app\common\wrappers\DynamicModal;

/**
 * Class ListView
 * @package Common\Widgets
 * @copyright 2012-2019 Medkey
 *
 */
class ListView extends \yii\widgets\ListView implements WidgetClientInterface, WrapperAbleInterface
{
    use IdWidgetTrait;
	use SectionableTrait;
    use WidgetClientTrait;
    use WrapperAbleTrait;

    /**
     * @var Model
     */
    public $filterModel;
    /**
     * @var array
     */
    public $options = [];
    /**
     * @var array
     */
    public $wrapperOptions;


    /**
     * ListView constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (is_array($config)) {
            $this->setConfig($config);
            parent::__construct($this->getConfig());
        } else {
            parent::__construct();
        }
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!isset($this->clientClassNameDefault)) {
            $this->clientClassNameDefault = ClassHelper::getShortName(__CLASS__);
        }
        if (empty($this->wrapperOptions)) {
            $this->wrapperOptions = $this->wrapperOptions();
        }
        if (($this->filterModel instanceof BaseFinder) && !isset($this->dataProvider)) {
            $this->dataProvider = $this->filterModel->search();
        }
        $page = \Yii::$app->request->getQueryParam('page');
        $sort = \Yii::$app->request->getQueryParam('sort');
        empty($page) ?: $this->clientViewOptions['page'] = $page;
        empty($sort) ?: $this->clientViewOptions['sort'] = $sort;
        parent::init();
        if ($this->clientView) {
            $this->registerClient($this->getId());
        }
    }

    public function run()
    {
        if ($this->showOnEmpty || $this->dataProvider->getCount() > 0) {
            $content = preg_replace_callback("/{\\w+}/", function ($matches) {
                $content = $this->renderSection($matches[0]);
                return $content === false ? $matches[0] : $content;
            }, $this->layout);
        } else {
            $content = $this->renderEmpty();
        }
        echo $content;
    }

    /**
     * Renders all data models.
     * @return string the rendering result
     */
    public function renderItems()
    {
        $models = $this->dataProvider->getModels();
        $keys = $this->dataProvider->getKeys();
        $rows = [];
        foreach (array_values($models) as $index => $model) {
            $rows[] = $this->renderItem($model, $keys[$index], $index);
        }

        return implode($this->separator, $rows);
    }

    /**
     * @inheritdoc
     */
    public function wrapperOptions()
    {
        return [
            'wrapperClass' => DynamicModal::className(),
        ];
    }
}
