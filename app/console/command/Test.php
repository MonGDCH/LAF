<?php

namespace app\console\command;

use mon\console\Input;
use mon\console\Output;
use mon\console\Command;

/**
 * 测试指令
 */
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
        return $out->write('Hello LAF Command!');
    }
}
