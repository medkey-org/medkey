<?php
namespace app\common\widgets;

use app\common\widgets\WidgetFactory;
use app\common\helpers\Html;
use yii\base\InvalidCallException;

/**
 * Class Panel
 * @package Common\Widgets
 * @copyright 2012-2019 Medkey
 *
 */
class Panel extends Widget
{
	const TYPE_DEFAULT = 'default';
	const TYPE_PRIMARY = 'primary';
	const TYPE_WARNING = 'warning';
	const TYPE_DANGER = 'danger';
	const TYPE_INFO = 'info';

	/**
	 * @var string
	 */
	public $header;
	/**
	 * @var string
	 */
	public $content;
	/**
	 * @var string
	 */
	public $type = self::TYPE_DEFAULT;
	/**
	 * @var bool
	 */
	public $collapsible = false;
	/**
	 * @var bool
	 */
	public $collapsed = false;
	/**
	 * @var array
	 */
	public $headingOptions = [];
	/**
	 * @var array
	 */
	public $bodyOptions = [];
	/**
	 * @var array
	 */
	public $wrapperCollapseHeaderOptions = [];
	/**
	 * @var array
	 */
	public $wrapperCollapseBodyOptions = [];
	/**
	 * @var bool
	 */
	public $encodeHeader = true;
	/**
	 * @var bool
	 */
	public $encodeContent = false;


	/**
	 * @inheritdoc
	 */
	public static function end()
	{
		if (!empty(static::$stack)) {
			$widget = array_pop(static::$stack);
			if (get_class($widget) === get_called_class()) {
				$widget->content = ob_get_clean();
				$content = $widget->run();

				if ($widget instanceof WidgetClientInterface && $widget->clientWrapperContainer) {
					$content = Html::tag('div', $content, $widget->options);
				}
				if ($widget instanceof WrapperAbleInterface && $widget->wrapper) {
					echo WidgetFactory::wrapperContent($content, $widget->wrapperOptions);
				} else {
					echo $content;
				}
				return $widget;
			} else {
				throw new InvalidCallException('Expecting end() of ' . get_class($widget) . ', found ' . get_called_class());
			}
		} else {
			throw new InvalidCallException('Unexpected ' . get_called_class() . '::end() call. A matching begin() is not found.');
		}
	}

	/**
	 * @inheritdoc
	 */
	public function run()
	{
		$this->getId();
		$options = $this->options;
		Html::addCssClass($options, "panel panel-{$this->type}");

		return Html::div($this->renderHeader() . $this->renderBody(), $options);
	}

	/**
	 * @return string
	 */
	protected function renderHeader()
	{
		$options = $this->headingOptions;
		Html::addCssClass($options, 'panel-heading');
		$header = $this->header;

		if ($this->encodeHeader) {
			$header = Html::encode($header);
		}
		if ($this->collapsible) {
			$header = $this->wrapCollapseHeader($header);
		}
		return Html::div($header, $options);
	}

	/**
	 * @return string
	 */
	protected function renderBody()
	{
		$options = $this->bodyOptions;
		Html::addCssClass($options, 'panel-body');
		$content = $this->content;

		if ($this->encodeContent) {
			$content = Html::encode($content);
		}
		$body = Html::div($content, $options);

		if ($this->collapsible) {
			$body = $this->wrapCollapseBody($body);
		}
		return $body;
	}

	/**
	 * @param string $header
	 * @return string
	 */
	protected function wrapCollapseHeader($header)
	{
		$options = $this->wrapperCollapseHeaderOptions;
		$options['data']['toggle'] = 'collapse';
		$options['aria-expanded'] = $this->collapsed ? 'false' : 'true';
		return Html::a($header, '#' . $this->getWrapperCollapseBodyId(), $options);
	}

	/**
	 * @param string $body
	 * @return string
	 */
	protected function wrapCollapseBody($body)
	{
		$options = $this->wrapperCollapseHeaderOptions;
		$options['id'] = $this->getWrapperCollapseBodyId();
		Html::addCssClass($options, 'panel-collapse collapse');

		if (!$this->collapsed) {
			Html::addCssClass($options, 'in');
		}
		return Html::div($body, $options);
	}

	/**
	 * @return string
	 */
	protected function getWrapperCollapseBodyId()
	{
		return $this->getId() . '-collapse';
	}
}
