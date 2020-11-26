<?php
namespace Laf\command;

use mon\env\Config as Env;
use mon\console\Command;
use mon\console\Input;
use mon\console\Output;

/**
 * 配置相关指令
 *
 * @author Mon <98555883@qq.com>
 * @version v1.0
 */
class Config extends Command
{
    /**
     * 执行指令
     *
     * @param  Input  $in  输入实例
     * @param  Output $out 输出实例
     * @return int         exit状态码
     */
    public function execute($in, $out)
    {
        // 获取查看的节点
        $args = $in->getArgs();
        $action = isset($args[0]) ? $args[0] : '';
        $out->write('');
        $config = Env::instance()->get($action);

        if (!is_null($action)) {
            return $out->dataList($config, $action);
        } else {
            foreach ($config as $title => $value) {
                $out->dataList($value, $title);
            }

            return 0;
        }
    }
}
