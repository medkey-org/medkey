<?php
namespace app\common\helpers;

use kartik\select2\Select2;
use app\common\widgets\MultipleInput;
use app\common\widgets\TimePicker;
use yii\base\Model;
use app\common\widgets\WidgetLoader;
use app\common\base\UniqueKey;
use app\common\db\ActiveQuery;
use app\common\db\ActiveRecord;
use app\common\web\View;
use app\common\widgets\DatePicker;
use app\common\widgets\DateTimePicker;
use yii\base\ErrorException;
use yii\db\ActiveRecordInterface;
use yii\helpers\Inflector;
use yii\widgets\MaskedInput;

/**
 * Class Html
 * @package Common\Helpers
 * @copyright 2012-2019 Medkey
 */
class Html extends \yii\bootstrap\Html
{
	const ALERT_SUCCESS = 'success';
	const ALERT_WARNING = 'warning';
	const ALERT_DANGER = 'danger';
	const ALERT_INFO = 'info';

	/**
	 * @see \yii\db\ColumnSchema::$type list of possible column types
	 * can be used `input` keys or `widget`
	 * If given both then will be used widget in priority
     * @var array
	 */
	protected static $inputTypeMapping = [
		'_default' => [],
		'_simpleSelect' => [
			'input' => 'dropDown',
		],
		'_hasOneRelation' => [
			'input' => 'dropDown',
		],
		'_hasManyRelation' => [
			'input' => 'dropDown',
		],
		'boolean' => ['input' => 'checkbox'],
		'text' => [
			'input' => 'textarea',
			'rows' => 5,
		],
		'date' => [
			'input' => 'date',
		],
		'datetime' => [
			'input' => 'dateTime',
		],
		'timestamp' => [
			'input' => 'dateTime',
		],
        'multiple' => [
            'input' => 'multiple',
        ],
	];


	/**
	 * @param ActiveRecord $model
	 * @param string       $attribute
	 * @param array        $options
	 * @return array
	 */
	public static function getInputType($model, $attribute, array $options = [])
	{
	    $type = [];
        static::addCssClass($type, 'form-control');
	    if (!$model instanceof ActiveRecordInterface) {
	        return $type;
        }
		$colType = ArrayHelper::getValue($options, 'type');

        if (!$colType && is_array($model->{$attribute})) {
            $colType = 'multiple';
        }

		if (!$colType) {
			$colType = $model::getTableSchema()->getColumn($attribute);

			if ($colType) {
				$colType = $colType->type;
			}
		}
		if (!$colType || !array_key_exists($colType, static::$inputTypeMapping)) {
			if (preg_match('/(_id|Id)$/', $attribute)) {
				$colType = '_hasOneRelation';
			} elseif (preg_match('/Ids$/', $attribute)) {
				$colType = '_hasManyRelation';
			} else {
				$colType = '_default';
			}
		}

		// Input for columns like status, priority, type
		// If exists method {attributeName}Labels or static method get{AttributeName}Labels
		if (!array_key_exists('items', $options)) {
			$method = Inflector::id2camel($attribute) . 'Labels';
			$staticMethod = 'get' . ucfirst(Inflector::id2camel($attribute)) . 'Labels';

			if (method_exists($model, $method)) {
				$colType = '_simpleSelect';
				$options['items'] = $model->{$method}();
			} elseif (method_exists($className = get_class($model), $staticMethod)) {
				$colType = '_simpleSelect';
				$options['items'] = call_user_func([$className, $staticMethod]);
			}
		}

		$type = static::$inputTypeMapping[$colType];
		$type = ArrayHelper::merge($type, $options);

		// Input for links. If attribute name like  *_id, *Id - hasOne, *Ids - hasMany
		if (in_array($colType, ['_hasOneRelation', '_hasManyRelation']) && !array_key_exists('items', $type)) {
			$relation = ArrayHelper::remove($type, 'relation');

			if (!$relation) {
				$relation = Inflector::id2camel(preg_replace('/(_id|Ids?)$/', '', $attribute));

				if ($colType === '_hasManyRelation') {
					$relation = Inflector::pluralize($relation);
				}
			}
			$method = 'get' . ucfirst($relation);

			if (method_exists($model, $method)) {
				$query = $model->{$method}();

				if ($query instanceof ActiveQuery) {
					$relationModel = $query->modelClass;
					$items = call_user_func([$relationModel, 'listAll']);
					$type['items'] = $items;
					$type['multiple'] = $query->multiple;
				}
			}
		}

		return $type;
	}

	/**
	 * @param ActiveRecord $model
	 * @param string       $attribute
	 * @param array        $options
	 * @return string
	 */
	public static function attributeInput($model, $attribute, array $options = [])
	{
		$type = static::getInputType($model, $attribute, $options);

		$input = ArrayHelper::remove($type, 'input', 'text');
		$widget = ArrayHelper::remove($type, 'widget');

		if ($widget) {
			/**
			 * @see \yii\widgets\InputWidget
			 */
			$type = array_merge($type, [
				'model' => $model,
				'attribute' => $attribute,
			]);

			return call_user_func([$widget, 'widget'], $type);
		}

		return static::activeInput($input, $model, $attribute, $type);
	}

	/**
	 * @inheritdoc
	 */
	public static function activeInput($type, $model, $attribute, $options = [])
	{
		$type = Inflector::id2camel($type);

		switch (strtolower(($type))) {
			case 'dropdown':
			case 'dropdownlist':
			case 'select':
			case 'listbox':
			case 'list':
				$items = ArrayHelper::remove($options, 'items', []);
				return static::activeDropDownList($model, $attribute, $items, $options);

			case 'radiolist': /* @see Html::activeRadioList() */
			case 'checkboxlist': /* @see Html::activeCheckboxList() */
				$items = ArrayHelper::remove($options, 'items', []);
				$method = 'active' . $type;
				return call_user_func([static::className(), $method], $model, $attribute, $items, $options);

			case 'radio': /* @see Html::activeRadio() */
			case 'checkbox': /* @see Html::activeCheckbox() */
			case 'textarea': /* @see Html::activeTextarea() */
				$method = 'active' . $type;
				return call_user_func([static::className(), $method], $model, $attribute, $options);

			case 'date': /* @see Html::activeDateInput() */
			case 'datetime': /* @see Html::activeDateTimeInput() */
				$method = 'active' . $type . 'Input';
                ArrayHelper::remove($options, 'type');
				return call_user_func([static::className(), $method], $model, $attribute, $options);

            case 'multiple':
                ArrayHelper::remove($options, 'type');
                return static::activeMultipleInput($model, $attribute, $options);
		}

		return parent::activeInput($type, $model, $attribute, $options);
	}

	public static function select2Input($name, $selection = '', array $items = [], array $options = [], array $pluginOptions = [])
    {
        if (!isset($pluginOptions['placeholder'])) {
            $pluginOptions['placeholder'] = \Yii::t('app', 'Select value...');
        }
        if (!isset($pluginOptions['allowClear'])) {
            $pluginOptions['allowClear'] = true;
        }
        if (!isset($options['empty'])) {
            $options['empty'] = false;
            $options['title'] = '';
        }
        return Select2::widget([
            'name' => $name,
            'value' => $selection,
            'data' => $items,
            'options' => $options,
            'pluginOptions' => $pluginOptions,
        ]);
    }

    /**
     * @param $model
     * @param $attribute
     * @param array $items
     * @param array $options
     * @param array $pluginOptions
     * @return string
     * @throws \Exception
     */
    public static function activeSelect2Input($model, $attribute, array $items = [], array $options = [], array $pluginOptions = [])
    {
//        $name = isset($options['name']) ? $options['name'] : static::getInputName($model, $attribute);
        if (!isset($pluginOptions['placeholder'])) {
            $pluginOptions['placeholder'] = \Yii::t('app', 'Select value...');
        }
        if (!isset($pluginOptions['allowClear'])) {
            $pluginOptions['allowClear'] = true;
        }
        if (!isset($options['empty'])) {
            $options['empty'] = false;
            $options['title'] = '';
        }
        return Select2::widget([
            'model' => $model,
            'attribute' => $attribute,
            'data' => $items,
            'options' => $options,
            'pluginOptions' => $pluginOptions
        ]);
    }

    /**
     * @param Model $model
     * @param string $attribute
     * @param array $options
     * @return string
     */
    public static function activeMultipleInput($model, $attribute, $options = [])
    {
        $name = isset($options['name']) ? $options['name'] : static::getInputName($model, $attribute);

        return MultipleInput::widget(array_merge($options, [
            'model' => $model,
            'data' => $model->{$attribute},
            'attribute' => $attribute,
            'name' => $name,
            'attributeOptions' => [
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'validateOnChange' => true,
                'validateOnSubmit' => true,
                'validateOnBlur' => true,
            ],
        ]));
    }

    /**
     * @param Model $model
     * @param string $attribute
     * @param array $options
     * @return string
     */
    public static function activeTimeInput($model, $attribute, $options = [])
    {
        $name = isset($options['name']) ? $options['name'] : static::getInputName($model, $attribute);

        if (isset($options['value'])) {
            $value = $options['value'];
            unset($options['value']);
        } else {
            $value = static::getAttributeValue($model, $attribute);
        }
        if (!array_key_exists('id', $options)) {
            $options['id'] = static::getInputId($model, $attribute);
        }

        return static::timeInput($name, $value, $options);
    }

    /**
     * @param $name
     * @param null $value
     * @param array $options
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public static function timeInput($name, $value = null, $options = [])
    {
        $options['name'] = $name;
        $options['value'] = \Yii::$app->formatter->asTime($value, CommonHelper::FORMAT_TIME_UI);
        return TimePicker::widget($options);
    }

    public static function moneyInput($name, $value = null, $options = [])
    {
        return MaskedInput::widget(['name' => $name, 'value' => $value, 'mask' => CommonHelper::CURRENCY_SUM_MASK, 'clientOptions' => ['greedy' => 'false']]);
    }

    public static function activeMoneyInput($model, $attribute, $options = [])
    {
        return MaskedInput::widget(['model' => $model, 'attribute' => $attribute, 'mask' => CommonHelper::CURRENCY_SUM_MASK, 'clientOptions' => ['greedy' => 'false']]);
    }

    /**
	 * @param Model  $model
	 * @param string $attribute
	 * @param array  $options
	 * @return string
	 */
	public static function activeDateInput($model, $attribute, $options = [])
	{
		$name = isset($options['name']) ? $options['name'] : static::getInputName($model, $attribute);

		if (isset($options['value'])) {
			$value = $options['value'];
			unset($options['value']);
		} else {
			$value = static::getAttributeValue($model, $attribute);
		}
		if (!array_key_exists('id', $options)) {
			$options['id'] = static::getInputId($model, $attribute);
		}

		return static::dateInput($name, $value, $options);
	}

	public static function dateInput($name, $value = null, $options = [])
	{
		$options['name'] = $name;
		if (empty($value)) {
		    $value = null;
        }
		$options['value'] = \Yii::$app->formatter->asDate($value, CommonHelper::FORMAT_DATE_UI);
		return DatePicker::widget($options);
	}

	/**
	 * @param Model  $model
	 * @param string $attribute
	 * @param array  $options
	 * @return string
	 */
	public static function activeDateTimeInput($model, $attribute, $options = [])
	{
		$name = isset($options['name']) ? $options['name'] : static::getInputName($model, $attribute);

		if (isset($options['value'])) {
			$value = $options['value'];
			unset($options['value']);
		} else {
			$value = static::getAttributeValue($model, $attribute);
		}
		if (!array_key_exists('id', $options)) {
			$options['id'] = static::getInputId($model, $attribute);
		}

		return static::dateTimeInput($name, $value, $options);
	}

    /**
     * @param $name
     * @param null $value
     * @param array $options
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
	public static function dateTimeInput($name, $value = null, $options = [])
	{
		$options['name'] = $name;
        if (empty($value)) {
            $value = null;
        }
        $options['value'] = \Yii::$app->formatter->asDatetime(empty($value) ? $value : $value . date_default_timezone_get(), CommonHelper::FORMAT_DATETIME_UI);
		return DateTimePicker::widget($options);
	}

	/**
	 * @return string
	 */
	public static function className()
	{
		return get_called_class();
	}

	/**
	 * @param string|array $msg
	 * @param array        $options
	 * @return string
	 */
	public static function alert($msg, array $options = [])
	{
		static::addCssClass($options, 'alert');

		if (is_array($msg)) {
			if (count($msg) == 2) {
				$options['header'] = array_shift($msg);
			}
			$msg = reset($msg);
		}
		$level = ArrayHelper::remove($options, 'level', self::ALERT_WARNING);
		$header = ArrayHelper::remove($options, 'header');
		$dismissible = ArrayHelper::remove($options, 'dismissible', true);
		$encode = ArrayHelper::remove($options, 'encode', true);

		if ($encode) {
			$msg = static::encode($msg);
		}
		if ($header) {
			if ($encode) {
				$header = static::encode($header);
			}
			$msg = static::tag('strong', $header) . ' ' . $msg;
		}
		if ($level) {
			static::addCssClass($options, "alert-$level");
		}
		if ($dismissible) {
			static::addCssClass($options, 'alert-dismissible');

			$msg = static::button('&times;', [
				'class' => 'close',
				'data' => ['dismiss' => 'alert'],
			]) . $msg;
		}

		return static::tag('div', $msg, $options);
	}

    /**
     * @todo check for afterUpdateBlockId
     * @param string $content
     * @param string $url
     * @param array $options
     * @param string $afterUpdateBlockId
     * @return string
     */
    public static function ajaxLink($content, $url, $options = [], $afterUpdateBlockId = null)
    {
        if (!isset($options['id'])) {
            $options['id'] = UniqueKey::generate('link');
        }
        $id = $options['id'];
        $js = <<<JS
$('#{$id}').on('click', function (e) {
    var target = $(e.currentTarget),
        el = $(this);
        // todo is href
	el.loading('loadingIcon');
    application.getComponent('request').ajax(el.attr('href')).done(function (html) {
        if (application.getWidgetById('{$afterUpdateBlockId}').length > 0) { // херня, но пока так
            application.getWidgetById('{$afterUpdateBlockId}').update();
        } else {
            application.getWidgetById('{$afterUpdateBlockId}').update();
        }
        el.loading('stopIcon');
    }).fail(function () {
        if (application.getWidgetById('{$afterUpdateBlockId}').length > 0) { // херня, но пока так
            application.getWidgetById('{$afterUpdateBlockId}').update();
        } else {
            application.getWidgetById('{$afterUpdateBlockId}').update();
        }
        el.loading('stopIcon');
    });
    
    return false;
});
JS;
        \Yii::$app->getView()->registerJs($js, View::POS_END);

        return static::a($content, $url, $options);
    }

    /**
     * @param $content
     * @param array $options
     * @param array $clientOptions
     * @param bool $isDynamicModel
     * @param null $queryParams
     * @param null $insertBlockId
     * @return mixed
     */
    public static function loaderButton($content, array $options = [], array $clientOptions = [], $isDynamicModel = true, $queryParams = null, $insertBlockId = null)
    {
        if (!isset($options['id'])) {
            $options['id'] = UniqueKey::generate('button');
        }
        WidgetLoader::widget([
            'options' => $options,
            'isDynamicModel' => $isDynamicModel,
            'queryParams' => $queryParams,
            'clientOptions' => $clientOptions,
            'insertBlockId' => $insertBlockId
        ]);

        return static::button($content, $options);
    }

    /**
     * @param $name
     * @param $content
     * @param array $options
     * @param array $clientOptions
     * @param bool $isDynamicModel
     * @param null $queryParams
     * @param null $insertBlockId
     * @return string
     */
    public static function loaderTag($name, $content, array $options = [], array $clientOptions = [], $isDynamicModel = true, $queryParams = null, $insertBlockId = null)
    {
        if (!isset($options['id'])) {
            $options['id'] = UniqueKey::generate('button');
        }

        WidgetLoader::widget([
            'options' => $options,
            'isDynamicModel' => $isDynamicModel,
            'queryParams' => $queryParams,
            'clientOptions' => $clientOptions,
            'insertBlockId' => $insertBlockId
        ]);

        return static::tag($name, $content, $options);
    }

	/**
	 * @param bool|null|string $name
	 * @param string $content
	 * @param array $options
	 * @return string
	 */
	public static function tag($name, $content = '', $options = [])
	{
		if (!empty($options['active'])) {
			unset($options['active']);
			Html::addCssClass($options, 'active');
		}
		if (!empty($options['icon'])) {
			if (strpos($options['icon'], 'glyphicon') === false) {
				$options['icon'] = 'glyphicon glyphicon-'. $options['icon'];
			}
			$icon = static::tag('span', '', [
				'class' => $options['icon'],
				'aria-hidden' => 'true',
			]);

			if (trim($content)) {
				$content = '&nbsp' . $content;
			}
			$content = $icon . $content;
			unset($options['icon']);
		}

		return parent::tag($name, $content, $options);
	}

    /**
     * @param string $name
     * @param null|int $monthBegin
     * @param null|int $monthEnd
     * @param null|int $selection
     * @param array $options
     * @return string
     */
	public static function dropDownMonth($name, $options = [], $selection = null, $monthBegin = null, $monthEnd = null)
    {
        $items = [];
        $months = [
            '1' => 'January',
            '2' => 'February',
            '3' => 'March',
            '4' => 'April',
            '5' => 'May',
            '6' => 'June',
            '7' => 'July',
            '8' => 'August',
            '9' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        ];
        if ($selection === null) {
            $selection = date('n');
        }
        if ($monthBegin === null && $monthEnd === null) {
            $monthBegin = 1;
            $monthEnd = 12;
        }
        for ($i = $monthBegin; $i <= $monthEnd; $i++) {
            $items[$i] = $months[$i];
        }
        $options['empty'] = false;

        return static::dropDownList($name, $selection, $items, $options);
    }

    /**
     * @param string$name
     * @param null|int $yearBegin
     * @param null|int $yearEnd
     * @param null|int $selection
     * @param array $options
     * @return string
     */
    public static function dropDownYear($name, $options = [], $selection = null, $yearBegin = null, $yearEnd = null)
    {
        $items = [];
        if ($selection === null) {
            $selection = (int)date('Y');
        }
        if ($yearBegin === null && $yearEnd === null) { // todo в будущем в параметр, пока exp не поддерижвается
            $yearBegin = (int)date('Y');
            $yearBegin--;
            $yearEnd = (int)date('Y');
            $yearEnd++;
        }
        for ($i = $yearBegin; $i <= $yearEnd; $i++) {
            $items[$i] = $i;
        }
        $options['empty'] = false;

        return static::dropDownList($name, $selection, $items, $options);
    }

	/**
	 * Bootstrap string
	 *
	 * Content example based on array:
	 * ['col1', 'col2']
	 * <div class="row">
	 *     <div class="col-md-6">col1</div>
	 *     <div class="col-md-6">col2</div>
	 * </div>
	 *
	 * [['content' => 'col1', 'size' => 2], ['content' => [['col3', 'col4']]]]
	 * <div class="row">
	 *     <div class="col-md-2">col1</div>
	 *     <div class="col-md-6">
	 *         <div class="row">
	 *             <div class="col-md-6">col3</div>
	 *             <div class="col-md-6">col4</div>
	 *         </div>
	 *     </div>
	 * </div>
	 *
	 * @param string|callable|array $content
	 * @param array                 $options
	 * @return string
	 */
    public static function row($content, array $options = [])
    {
	    $content = CommonHelper::value($content);

	    if (is_array($content)) {
		    $content = static::cols($content);
	    }

	    return static::beginRow($options) . $content . static::endRow();
    }

	/**
	 * @param array $items
	 * @return string
	 */
    public static function rows(array $items)
    {
	    return implode("\n", array_map(function ($options) {
		    if (ArrayHelper::isAssociative($options)) {
			    $content = ArrayHelper::remove($options, 'content');
		    } else {
			    $content = $options;
			    $options = [];
		    }

		    return static::row($content, $options);
	    }, $items));
    }

	/**
	 * Bootstrap строка
	 *
	 * @param array $options
	 * @return string
	 */
    public static function beginRow(array $options = [])
    {
	    static::addCssClass($options, 'row');

	    return static::beginDiv($options);
    }

	/**
	 * @return string
	 */
    public static function endRow()
    {
	    return static::endDiv();
    }

	/**
	 * Bootstrap column
	 *
	 * Пример
	 * ['size' => 3]                                        col-md-3
	 * ['size' => ['sm' => 6, 'md' => 4, 'md-offset' => 2]] col-sm-6 col-md-4 col-md-offset-2
	 *
	 * @param string|callable|array $content
	 * @param array                 $options
	 * @return string
	 */
    public static function col($content, array $options = [])
    {
	    $content = CommonHelper::value($content);

	    if (is_array($content)) {
		    $content = static::rows($content);
	    }

	    return static::beginCol($options) . $content . static::endCol();
    }

	/**
	 * @param array $items
	 * @return string
	 */
    public static function cols(array $items)
    {
	    list($free, $count) = array_reduce($items, function ($item, $options) {
		    if (is_array($options) && array_key_exists('size', $options)) {
			    $item[0] -= (int) $options['size'];
			    --$item[1];
		    }
		    return $item;
	    }, [12, count($items)]);

	    if ($count <= 0) {
		    $defaultSize = 0;
	    } else {
		    $defaultSize = (int) floor($free / $count);
	    }

	    return implode("\n", array_map(function ($options) use ($defaultSize) {
		    if (!is_array($options)) {
			    $options = ['content' => $options];
		    }
		    if (ArrayHelper::isAssociative($options)) {
			    $content = ArrayHelper::remove($options, 'content');
		    } else {
			    $content = $options;
			    $options = [];
		    }
		    if (!array_key_exists('size', $options)) {
			    $options['size'] = $defaultSize;
		    }

		    return static::col($content, $options);
	    }, $items));
    }

	/**
	 * Bootstrap column
	 *
	 * Пример
	 * ['size' => 3]                                        col-md-3
	 * ['size' => ['sm' => 6, 'md' => 4, 'md-offset' => 2]] col-sm-6 col-md-4 col-md-offset-2
	 *
	 * @param array $options
	 * @return string
	 */
    public static function beginCol(array $options = [])
    {
	    $sizes = (array) ArrayHelper::remove($options, 'size', 12);
	    $classes = array_map(function ($size, $type) {
		    if (is_numeric($type)) {
			    $type = 'md';
		    }
		    return "col-$type-$size";
	    }, $sizes, array_keys($sizes));
	    static::addCssClass($options, $classes);

	    return static::beginDiv($options);
    }

	/**
	 * @return string
	 */
    public static function endCol()
    {
	    return static::endDiv();
    }

	/**
	 * Bootstrap container
	 *
	 * @param string|callable|array $content
	 * @param array                 $options
	 * @return string
	 */
    public static function container($content, array $options = [])
    {
	    $content = CommonHelper::value($content);

	    if (is_array($content)) {
		    $content = static::rows($content);
	    }

	    return static::beginContainer($options) . $content . static::endContainer();
    }

	/**
	 * Bootstrap container begin
	 *
	 * @param array $options
	 * @return string
	 */
    public static function beginContainer(array $options = [])
    {
	    $fluid = ArrayHelper::remove($options, 'fluid', true);

	    if ($fluid) {
		    static::addCssClass($options, 'container-fluid');
	    } else {
		    static::addCssClass($options, 'container');
	    }

	    return static::beginDiv($options);
    }

	/**
	 * @return string
	 */
	public static function endContainer()
	{
		return static::endDiv();
	}

	/**
	 * @param string|callable $content
	 * @param array           $options
	 * @return string
	 */
	public static function div($content, array $options = [])
	{
		$content = CommonHelper::value($content);
		return static::beginDiv($options) . $content . static::endDiv();
	}

	/**
	 * @param array $options
	 * @return string
	 */
	public static function beginDiv(array $options = [])
	{
		return static::beginTag('div', $options);
	}

	/**
	 * @return string
	 */
	public static function endDiv()
	{
		return static::endTag('div');
	}

    /**
     * @param $options
     * @return string
     */
    public static function iframe($options)
    {
        return static::beginTag('iframe', $options).
            static::endTag('iframe');
    }
}
