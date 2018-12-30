<?php
namespace app\common\widgets;

/**
 * Class Modal
 * @package Common\Widgets
 * @copyright 2012-2019 Medkey
 *
 */
class Modal extends \yii\bootstrap\Modal
{
	use IdWidgetTrait;

	/**
	 * @var array
	 */
    public $headerOptions = [
        'style' => 'font-size: 18px;'
    ];
	/**
	 * @var array
	 */
	public $headerButtons = [];
	/**
	 * @var string
	 */
	public $headerButtonsTemplate = '';
	/**
	 * @var string
	 */
	public $headerButtonsAppend = true;


	public function init()
    {
        $this->options['tabindex'] = false;
        parent::init();
    }

	/**
	 * @inheritdoc
	 */
	protected function renderHeader()
	{
		$buttons = $this->renderHeaderButtons();

		if ($buttons !== null) {
			$this->header = $this->headerButtonsAppend ? ($this->header . "\n" . $buttons) : ($buttons . "\n" . $this->header);
		}

		return parent::renderHeader();
	}

	/**
	 * @return string|null
	 */
	protected function renderHeaderButtons()
	{
		if (!$this->headerButtons || !$this->headerButtonsTemplate){
			return null;
		}

		return preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) {
			$name = $matches[1];

			if (isset($this->headerButtons[$name])) {
				return call_user_func($this->headerButtons[$name]);
			} else {
				return '';
			}
		}, $this->headerButtonsTemplate);
	}
}
