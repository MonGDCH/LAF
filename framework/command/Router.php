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
        $action = $in->getArgs()[0] ?? 'help';
        $out->write('');
        // 执行回调
        switch ($action) {
            case 'clear':
                return $this->clear($in, $out);
                break;
            case 'cache':
                return $this->cache($in, $out);
                break;
            case 'show':
                return $this->show($in, $out);
                break;
            case 'help':
                return $this->help($in, $out);
            default:
                $out->block('action is not found!', 'error');
                $out->write('');
                return $this->help($in, $out);
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
        $columns = ['method', 'path', 'befor', 'callback', 'append'];

        $data = Route::instance()->getData();
        $res = [];
        foreach ($data[0] as $method => $item) {
            foreach ($item as $path => $info) {
                $res[] = [
                    'method'    => $method,
                    'path'      => $path,
                    'befor' => $info['befor'],
                    'callback'  => is_string($info['callback']) ? $info['callback'] : '- Closure Function',
                    'append'    => $info['append'],
                ];
            }
        }

        return $out->table($res, 'Router Table', $columns);
    }

    /**
     * 缓存路由
     *
     * @param  [type] $in  [description]
     * @param  [type] $out [description]
     * @return [type]      [description]
     */
    protected function cache($in, $out)
    {
        $cache = Route::instance()->cache(ROUTE_CACHE);
        if (!$cache) {
            return $out->block('build route cache error!', 'ERROR');
        }

        return $out->block('build route cache success!', 'SUCCESS');
    }

    /**
     * 清除路由缓存
     *
     * @param  [type] $in  [description]
     * @param  [type] $out [description]
     * @return [type]      [description]
     */
    protected function clear($in, $out)
    {
        if (!file_exists(ROUTE_CACHE)) {
            return $out->block('route cache file is not exists', 'INFO');
        }
        if (!unlink(ROUTE_CACHE)) {
            return $out->block('clear route cache error!', 'ERROR');
        }

        return $out->block('clear route cache success!', 'SUCCESS');
    }

    /**
     * 指令帮助
     *
     * @param  [type] $in  [description]
     * @param  [type] $out [description]
     * @return [type]      [description]
     */
    protected function help($in, $out)
    {
        $help = [
            'show help: '   => 'php laf route help',
            'show route: '  => 'php laf route show',
            'cache route: ' => 'php laf route cache',
            'clear route: ' => 'php laf route clear',
        ];

        return $out->list($help, 'route command help');
    }
}
