<?php
namespace Laf\command;

use FApi\Route;
use Mon\console\Command;
use Mon\console\Input;
use Mon\console\Output;

/**
 * 路由相关指令
 *
 * @author Mon <98555883@qq.com>
 * @version v1.0
 */
class Router extends Command
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
        // 获取执行的回调
        $action = $in->getArgs()[0] ?? 'show';

        // 执行回调
        switch($action)
        {
            case 'show':    
            default:
                return $this->show($in, $out);
                break;
        }
    }

    /**
     * 显示路由列表
     *
     * @return [type] [description]
     */
    protected function show($in, $out)
    {
        $columns = ['method', 'path', 'middleware', 'callback', 'append'];

        $data = Route::instance()->getData();
        $res = [];
        foreach($data[0] as $method => $item)
        {
            foreach($item as $path => $info)
            {
                $res[] = [
                    'method'    => $method,
                    'path'      => $path,
                    'middleware'=> $info['middleware'],
                    'callback'  => is_string($info['callback']) ? $info['callback'] : '- Closure Function',
                    'append'    => $info['append'],
                ];
            }
        }

        $out->write('');
        return $out->table($res, 'Router Table', $columns);
    }
}