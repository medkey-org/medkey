<?php
namespace app\common\widgets;

use app\common\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * Class LayoutWidget
 * @package Common\Widgets
 * @copyright 2012-2019 Medkey
 *
 */
class LayoutWidget extends Widget
{
	use SectionableTrait;

	/**
	 * @var string
	 */
	public $layout = '';

	/**
	 * @inheritdoc
	 */
	public function run()
	{
		$content = preg_replace_callback('/{\w+}/', function ($matches) {
			$content = $this->renderSection($matches[0]);

			return $content === false ? '' : $content;
		}, $this->layout);

		$options = $this->options;
		$tag = ArrayHelper::remove($options, 'tag', 'div');

		echo Html::tag($tag, $content, $options);
	}
}
