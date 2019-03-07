<?php
namespace App\Console\Command;

use Mon\console\Command;
use Mon\console\Input;
use Mon\console\Output;

class Test extends Command
{
    /**
     * 执行指令
     *
     * @param  Input  $in  输入实例
     * @param  Output $out 输出实例
     * @return int         exit状态码
     */
    public function execute(Input $in, Output $out)
    {
        return $out->write('This is Test Command!');
    }
}