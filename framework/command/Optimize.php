<?php
namespace Laf\command;

use FApi\Route;
use mon\util\File;
use mon\env\Config;
use mon\console\Command;

/**
 * 应用优化指令
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0   2022-07-15
 */
class Optimize extends Command
{
    /**
     * 执行指令
     *
     * @param  Input  $in  输入实例
     * @param  Output $out 输出实例
     * @return integer
     */
    public function execute($in, $out)
    {
        // 缓存路由
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

        // 缓存配置文件
        $config = Config::instance()->get();
        $content = '<?php ' . PHP_EOL . 'return ' . var_export($config, true) . ';';
        $save = File::instance()->createFile($content, CONFIG_CACHE, false);
        if (!$save) {
            return $out->block('build config cache error!', 'ERROR');
        }

        return $out->block('Successed!', 'SUCCESS');
    }
}
