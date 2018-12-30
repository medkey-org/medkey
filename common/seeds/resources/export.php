<?php

/**
 * @var string $namespace
 * @var string $className
 * @var \app\common\db\ActiveRecord[] $models
 *
 * @package Common\Seed
 * @copyright 2012-2019 Medkey
 *
 */

$ignore = ['id', 'user_created', 'user_updated', 'created_at', 'updated_at', 'deleted_at', 'is_deleted', 'history_version', 'history_start_date', 'union_id'];

echo '<' . '?php';
?>

namespace <?php echo $namespace; ?>;

use app\common\seeds\Seed;

class <?php echo $className; ?> extends Seed
{
	public function run()
	{
		$this->model = \app\common\logic\orm\<?php echo $className; ?>::className();

		$this->data = [
		<?php foreach ($models as $model): ?>
			[
			<?php foreach (array_diff($model->attributes(), $ignore) as $attr): ?>
				"<?php echo $attr ?>" => "<?php echo addslashes($model->{$attr}); ?>",
			<?php endforeach; ?>
			],
		<?php endforeach; ?>
		];

	}
}
