<?php

namespace app\console\command;

use mon\console\Command;
use mon\console\Input;
use mon\console\Output;

class Test extends Command
{
    /**
     * 执行指令
     *
     * @param  Input  $in  输入实例
     * @param  Output $out 输出实例
     * @return integer exit状态码
     */
    public function execute($in, $out)
    {
        return $out->write('This is Test Command!');
    }
}
