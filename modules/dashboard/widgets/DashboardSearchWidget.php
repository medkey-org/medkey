<?php
namespace app\modules\dashboard\widgets;

use app\common\helpers\Html;
use app\modules\dashboard\models\finders\DashboardFinder;
use app\modules\dashboard\models\orm\Dashboard;
use app\common\widgets\SearchWidget;
use app\modules\dashboard\widgets\grid\DashboardGrid;
use yii\helpers\ArrayHelper;

/**
 * Class DashboardSearchWidget
 * @package Module\Dashboard
 * @copyright 2012-2019 Medkey
 */
class DashboardSearchWidget extends SearchWidget
{
    /**
     * @var DashboardFinder
     */
    public $model;
    /**
     * @var array
     */
    public $grid_options = [];
    /**
     * @var array
     */
    public $fields = ['title', 'type', 'q'];
    /**
     * @var bool
     */
    public $renderResetAfterForm = false;


    /**
     * @return void
     */
    public function init()
    {
//        $this->model = DashboardFinder::ensure($this->model, 'search');
        parent::init();
    }

    /**
     * Фильтруемый виджет
     * @return mixed
     */
    protected function renderSearchingWidget()
    {
        return DashboardGrid::widget(ArrayHelper::merge($this->grid_options, [
            'filterModel' => $this->model,
        ]));
    }

    /**
     * @param \yii\base\Model $model
     * @param \app\common\widgets\ActiveForm $form
     */
    public function renderForm($model, $form) {
        echo Html::row([[
            'size' => 2,
            'content' => $form->field($model, 'types')->checkboxList(Dashboard::getTypeLabels()),
        ], [
            'size' => 6,
            'content' => [[
                $form->field($model, 'q'),
            ],
            [
                [[
                    Html::submitButton('Найти', [
                        'icon' => 'search',
                        'class' => 'btn btn-primary btn-block btn-search',
                    ]),
                    Html::resetButton('Очистить', [
                        'class' => 'btn btn-default btn-block form-trash btn-clear',
                        'icon' => 'glyphicon glyphicon-trash',
                    ]),
                ]],
            ]],
        ]]);
    }
}