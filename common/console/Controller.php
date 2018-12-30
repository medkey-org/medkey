<?php
namespace app\common\console;

use yii\helpers\Console;

/**
 * Class Controller
 * @package Common\Console
 * @copyright 2012-2019 Medkey
 */
class Controller extends \yii\console\Controller
{
    /**
     * @param string $prompt
     * @return string
     */
    public function multilinePrompt($prompt = null)
    {
        if (!is_null($prompt)) {
            $this->line($prompt . ' (for end enter a empty line)');
        }
        $parts = [];

        while ($line = readline()) {
            $parts[] = $line;
        }

        return implode("\n", $parts);
    }

    /**
     * @param  string $string
     * @return void
     */
    public function line($string)
    {
        echo $string . PHP_EOL;
    }

    /**
     * @return void
     */
    public function blankLine()
    {
        $this->line('');
    }

    /**
     * @param string $string
     */
    public function error($string)
    {
        Console::error($string);
    }
}
