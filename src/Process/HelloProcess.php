<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2017
 *
 * @see      https://www.github.com/janhuang
 * @see      http://www.fast-d.cn/
 */

namespace Process;

use FastD\Process\AbstractProcess;
use swoole_process;

class HelloProcess extends AbstractProcess
{
    public function handle(swoole_process $swoole_process)
    {
        timer_tick(1000, function ($id) {
            static $index = 0;
            ++$index;
            echo $index.PHP_EOL;
            if ($index === 3) {
                timer_clear($id);
            }
        });
    }
}