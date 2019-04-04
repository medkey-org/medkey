<?php
namespace app\commands;

use app\common\console\Controller;
use app\common\seeds\Seed;
use app\common\seeds\Seeder;

/**
 * Class SeedController
 * @package Common\CLI
 * @copyright 2012-2019 Medkey
 */
class SeedController extends Controller
{
	/**
	 * @var string
	 */
	public $defaultAction = 'apply';
	/**
	 * @var string
	 */
	public $defaultFilename = null;


	/**
     * Example: php bin seed/apply Package.
	 */
	public function actionApply($className = null, $pathPart = null)
	{
		if (!$className) {
		    return $this->stdout("Enter seed name." . PHP_EOL);
//			$className = $this->defaultFilename;
		}
		$this->seeder($pathPart)->safeApply($className, function (Seed $seed) {
			$className = get_class($seed);
			$this->line("Seed `{$className}` successfully applied");
		});

		return 0;
	}

	/**
	 * Create seed.
	 */
	public function actionCreate($className, $pathPart = null)
	{
		$fileName = $this->seeder($pathPart)->create($className);
		$this->line("Seed `{$fileName}` successfully created");

		return 0;
	}

	/**
	 * List of seeds package.
	 */
	public function actionList($limit = null)
	{
		$seeds = $this->seeder()->getList($limit);

		foreach ($seeds as $seed) {
			$this->line($seed->name);
		}
	}

	/**
	 *
	 */
//	public function actionExport($models)
//	{
//		$models = explode(',', $models);
//		$files = $this->seeder()->export($models, 'app\seeds\export', '@app/seeds/export');
//
//		foreach ($files as $file) {
//			$this->line("Export seed `{$file}` successfully created");
//		}
//	}

    /**
     * @param null $pathPart
     * @return Seeder
     * @throws \yii\base\InvalidConfigException
     */
	protected function seeder($pathPart = null)
	{
		/**
		 * @var Seeder $seeder
		 */
		$seeder = \Yii::$app->get('seeder');

		if ($pathPart) {
			$seeder->path .= DIRECTORY_SEPARATOR . $pathPart;
			$seeder->namespace .= '\\' . implode('\\', explode(DIRECTORY_SEPARATOR, $pathPart));
		}

		return $seeder;
	}
}
