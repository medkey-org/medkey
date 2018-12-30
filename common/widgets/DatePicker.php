<?php
namespace app\common\widgets;

use app\common\helpers\CommonHelper;
use app\common\helpers\Html;
use yii\helpers\ArrayHelper;
use app\common\widgets\WidgetClientInterface;
use app\common\widgets\IdWidgetTrait;
use app\common\widgets\WidgetClientTrait;

/**
 * Class DatePicker
 * @package Common\Widgets
 * @copyright 2012-2019 Medkey
 *
 */
class DatePicker extends \kartik\date\DatePicker
{
	/**
	 * @var array
	 */
	public $pluginOptions = [
		'format' => CommonHelper::FORMAT_DATEPICKER_UI,
		'todayHighlight' => true,
        'autoclose' => true,
	];
    /**
     * @var array
     */
	public $options = [
	    'autocomplete' => 'off',
    ];
	/**
	 * @var int
	 */
	public $type = self::TYPE_COMPONENT_APPEND;
	/**
	 * @var bool
	 */
	public $startAfterNow = true;
    /**
     * @var bool
     */
    public $startBeforeNow = false;

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		if ($this->startAfterNow && !array_key_exists('startDate', $this->pluginOptions)) {
			$this->pluginOptions['startDate'] = \Yii::$app->formatter->asDate(time(), CommonHelper::FORMAT_DATETIME_DB);
		}
        if ($this->startBeforeNow && !array_key_exists('endDate', $this->pluginOptions)) {
            $this->pluginOptions['endDate'] = \Yii::$app->formatter->asDate(time(), CommonHelper::FORMAT_DATETIME_DB);
        }
		parent::init();
	}

    /**
     * @inheritdoc
     */
    public function setLanguage($prefix, $assetPath = null, $filePath = null, $suffix = '.js')
    {
        // for webpack
        $this->_langFile = '';
    }
}
