<?php
namespace app\common\widgets;

use app\common\helpers\Html;

/**
 * Class ActiveForm
 * @package Common\Widgets
 * @copyright 2012-2019 Medkey
 *
 */
class ActiveForm extends \yii\bootstrap\ActiveForm
{
    use IdWidgetTrait;

    const REQUIRED_CLASS = 'required';

    /**
     * @var string
     */
    public $requiredCssClass = self::REQUIRED_CLASS;
    /**
     * @var string
     */
    public $fieldClass = ActiveField::class;
    /**
     * @var bool
     */
    public $enableAjaxValidation = true;
    /**
     * @var bool
     */
    public $enableClientValidation = true;
    /**
     * For native yii validate
     * @var string
     */
    public $ajaxParam = 'ajaxValidate';
    /**
     * @var bool
     */
    public $afterRedirect = false;
    /**
     * @var string
     */
    public $redirectUrl;


    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->afterRedirect) {
            print Html::tag('input', '', [
                'type' => 'hidden',
                'name' => '_redirectUrl',
                'value' => $this->redirectUrl,
            ]);
        }
        parent::run();
    }

	/**
	 * @param \yii\base\Model $model
	 * @param string          $attribute
	 * @param array           $options
	 * @return ActiveField
	 */
    public function field($model, $attribute, $options = [])
    {
	    return parent::field($model, $attribute, $options);
    }

	/**
	 * @param \yii\base\Model        $model
	 * @param array|array[]|string[] $attributes
	 * @return string
	 */
//    public function fields($model, $attributes)
//    {
//	    return implode("\n", array_map(function ($options) use ($model) {
//		    if (!is_array($options)) {
//			    $options = ['attribute' => $options];
//		    }
//		    $attr = ArrayHelper::remove($options, 'attribute');
//		    $type = ArrayHelper::remove($options, 'type', 'attributeInput');
//
//		    return $this->field($model, $attr, $options)->$type();
//	    }, $attributes));
//    }
}
