<?php
namespace app\modules\dashboard\widgets;

use app\common\helpers\ArrayHelper;
use app\common\helpers\Html;
use app\modules\dashboard\models\orm\Dashboard;
use app\modules\dashboard\models\orm\DashboardItem;
use app\common\widgets\DetailView;
use app\common\widgets\ImitatedWidgetInterface;
use app\common\widgets\ImitatedWidgetTrait;
use Yii;

/**
 * Class DashboardDetail
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class DashboardDetail extends DetailView implements ImitatedWidgetInterface
{
	use ImitatedWidgetTrait;

    /**
     * @var array
     */
    public $renderedDashlets = [];
	/**
	 * @var Dashboard
	 */
    public $model;
    /**
     * @var array
     */
    public $dashletOptions = [];
    /**
     * @var bool
     */
    public $clientWrapperContainer = true;
    /**
     * @var string
     */
    public $module = 'dashboard';
    /**
     * @var array
     */
    public $options = ['class' => ''];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->model = Dashboard::ensure($this->model);

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->imitation) {
            return;
        }

        $dashboardItems = $this->model->dashboardItems;

//	    if (!DashboardItem::gate()->can('view')) {
//		    // фильтруем недоступные дашлеты
//		    $dashboardItems = ArrayHelper::filterBy($dashboardItems, function (DashboardItem $model) {
//			    // через гейт, чтоб избежать повторных проверок на уровне класса
//			    return $model->gate->can('view');
//		    });
//	    }



        foreach ($dashboardItems as $dashboardItem) {
            $this->renderDashboardItem($dashboardItem);
        }

        $filename = '@app/modules/dashboard/layouts/' . $this->model->layout . '.php';

        if (file_exists(Yii::getAlias($filename))) {
            echo $this->renderFile($filename, [
                'dashlets' => $this->renderedDashlets
            ]);
        }
    }

    /**
     * @param DashboardItem $dashboardItem
     * @return void
     */
    public function renderDashboardItem(DashboardItem $dashboardItem)
    {
	    /** @var Dashlet $dashletClass */
        $dashletClass = $dashboardItem->getWidgetClass();

        $dashletTitle = Html::tag('div', $dashboardItem->title, ['class' => 'box-title']);

        // Мы не ожидаем двух dashlet_item с однинаковыми position и order, т.к. это должно проверяться на этапе валидации
        $this->renderedDashlets[$dashboardItem->position][$dashboardItem->order] =
            Html::beginTag('div', ['class' => 'white-box']) .
            $dashletTitle . Html::tag('div',
                $dashletClass::widget(array_merge(
                    isset($this->dashletOptions[$dashboardItem->id]) ? $this->dashletOptions[$dashboardItem->id] : [],
                    [
                        'dashboardItemId' => $dashboardItem->id,
                    ]
                )), ['class' => 'box-content']) .
            Html::endTag('div');
    }

    /**
     * Имитировать работу виджета - виджет должен отключить все управляющие элементы и показать пустые данные
     *
     * @return void
     */
    public function imitate()
    {
	    $this->model = new Dashboard();
    }
}
