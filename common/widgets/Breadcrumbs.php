<?php
namespace app\common\widgets;

use app\common\helpers\StringHelper;

/**
 * Class Breadcrumbs
 * @package Common\Widgets
 * @copyright 2012-2019 Medkey
 *
 */
class Breadcrumbs extends \yii\widgets\Breadcrumbs
{
	public function init()
	{
		$this->links = array_map(function ($link) {
			if (!is_array($link)) {
				$link = ['label' => $link];
			}
			if (array_key_exists('label', $link)) {
				$link['label'] = StringHelper::truncate($link['label'], 50);
			}
			return $link;
		}, (array) $this->links);

		parent::init();
	}
}
