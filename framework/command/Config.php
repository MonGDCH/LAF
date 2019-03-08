<?php
namespace Laf\command;

use FApi\Container;
use Mon\console\Command;
use Mon\console\Input;
use Mon\console\Output;

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
    public function execute(Input $in, Output $out)
    {
        // 获取查看的节点
        $action = $in->getArgs()[0] ?? null;
        $out->write('');
        $config = Container::instance()->config->get($action);
        
        if(!is_null($action)){
            return $out->list($config, $action);
        }
        else{
            foreach($config as $title => $value)
            {
                $out->list($value, $title);
            }

            return 0;
        }
    }
}