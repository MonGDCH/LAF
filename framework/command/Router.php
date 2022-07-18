<?php

namespace Laf\command;

use FApi\Route;
use mon\util\File;
use mon\console\Input;
use mon\console\Output;
use mon\console\Command;
use FastRoute\Dispatcher;

/**
 * 路由相关指令
 *
 * @author Mon <98555883@qq.com>
 * @version 1.0.0
 */
class Router extends Command
{
    /**
     * 执行指令
     *
     * @param  Input  $in  输入实例
     * @param  Output $out 输出实例
     * @return integer  exit状态码
     */
    public function execute($in, $out)
    {
        // 获取执行的回调
        $args = $in->getArgs();
        $action = isset($args[0]) ? $args[0] : 'help';
        $out->write('');
        // 执行回调
        switch ($action) {
            case 'test':
                return $this->test($in, $out);
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
     * 测试路径，获取路由回调
     *
     * @param  Input  $in  输入实例
     * @param  Output $out 输出实例
     * @return integer
     */
    public function test($in, $out)
    {
        // $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];
        $args = $in->getArgs();
        $method = 'GET';
        if (isset($args[1]) && isset($args[2])) {
            $method = strtoupper($args[1]);
            $path = $args['2'];
        } else if (isset($args[1])) {
            $path = $args[1];
        } else {
            return $out->block('please input test uri pathinfo', 'ERROR');
        }

        $columns = ['method', 'path', 'befor', 'callback', 'append'];
        $callback = Route::instance()->dispatch($method, $path);
        switch ($callback[0]) {
                // 200 匹配请求
            case Dispatcher::FOUND:
                $info = $callback[1];
                $table = [];
                $table[] = [
                    'method'    => $method,
                    'path'      => $path,
                    'befor'     => isset($info['befor']) ? implode(',', $info['befor']) : '',
                    'callback'  => is_string($info['callback']) ? $info['callback'] : '- Closure Function',
                    'append'    => isset($info['append']) ? implode(',', $info['append']) : '',
                ];
                return $out->table($table, 'Callback Table', $columns);

                // 405 Method Not Allowed  方法不允许
            case Dispatcher::METHOD_NOT_ALLOWED:
                // 允许调用的请求类型
                $allowedMethods = implode(',', (array)$callback[1]);
                return $out->block('Route method is not found![' . $allowedMethods . ']', 'ERROR');
                // 404 Not Found 没找到对应的方法
            case Dispatcher::NOT_FOUND:
                $default = Route::instance()->dispatch($method, '*');
                if ($default[0] === Dispatcher::FOUND) {
                    $info = $default[1];
                    $table = [];
                    $table[] = [
                        'method'    => $method,
                        'path'      => $path,
                        'befor'     => isset($info['befor']) ? implode(',', $info['befor']) : '',
                        'callback'  => is_string($info['callback']) ? $info['callback'] : '- Closure Function',
                        'append'    => isset($info['append']) ? implode(',', $info['append']) : '',
                    ];
                    return $out->table($table, 'Callback Table', $columns);
                }
                return $out->block('Route is not found', 'ERROR');
                // 不存在路由定义
            default:
                return $out->block('Route is not found', 'ERROR');
        }
    }

    /**
     * 显示路由列表
     *
     * @param  Input  $in  输入实例
     * @param  Output $out 输出实例
     * @return integer
     */
    protected function show($in, $out)
    {
        $columns = ['method', 'path', 'befor', 'callback', 'after'];

        $data = Route::instance()->getData();
        $res = [];
        foreach ($data[0] as $method => $item) {
            foreach ($item as $path => $info) {
                $res[] = [
                    'method'    => $method,
                    'path'      => $path,
                    'befor'     => isset($info['befor']) ? implode(',', $info['befor']) : '',
                    'callback'  => is_string($info['callback']) ? $info['callback'] : '- Closure Function',
                    'after'     => isset($info['after']) ? implode(',', $info['after']) : '',
                ];
            }
        }

        return $out->table($res, 'Router Table', $columns);
    }

    /**
     * 缓存路由
     *
     * @param  Input  $in  输入实例
     * @param  Output $out 输出实例
     * @return integer
     */
    protected function cache($in, $out)
    {
        // 获取路由信息
        $cache = Route::instance()->cache();
        if (!$cache) {
            return $out->block('get route info error!', 'ERROR');
        }
        // 生成路由文件
        $content = '<?php ' . PHP_EOL . 'return ' . $cache . ';';
        $save = File::instance()->createFile($content, ROUTE_CACHE, false);
        if (!$save) {
            return $out->block('build route cache error!', 'ERROR');
        }

        return $out->block('build route cache success!', 'SUCCESS');
    }

    /**
     * 清除路由缓存
     *
     * @param  Input  $in  输入实例
     * @param  Output $out 输出实例
     * @return integer
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
     * @param  Input  $in  输入实例
     * @param  Output $out 输出实例
     * @return integer
     */
    protected function help($in, $out)
    {
        $help = [
            'test url: '    => ' php laf router test [methods] pathinfo',
            'show help: '   => ' php laf route help',
            'show route: '  => ' php laf route show',
            'cache route: ' => ' php laf route cache',
            'clear route: ' => ' php laf route clear',
        ];

        return $out->dataList($help, 'route command help');
    }
}
