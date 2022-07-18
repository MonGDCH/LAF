<?php

namespace Laf;

use mon\env\Config;
use mon\util\Instance;
use mon\util\Log as Service;

/**
 * 日志处理类
 * 
 * @method array getConfig() getConfig 获取配送信息
 * @method Service setConfig(array $config) 定义日志配置信息
 * @method Service record(string $message, string $type = Log::INFO, boolean $trace = false, integer $level = 1) 记录日志信息
 * @method Service debug(string $message, boolean $trace = false) 记录调试信息
 * @method Service info(string $message, boolean $trace = false) 记录一般信息
 * @method Service notice(string $message, boolean $trace = false) 记录通知信息
 * @method Service warning(string $message, boolean $trace = false) 记录警告信息
 * @method Service error(string $message, boolean $trace = false) 批量写入日志
 * @method array getLog() 获取日志信息
 * @method boolean save() 保存日志信息
 * @method Service clear() 清空日志信息
 * 
 * @author mon <985558837@qq.com>
 * @version 2.0.0 新版本 2022-07-15
 */
class Log extends Provider
{
    use Instance;

    public function getService()
    {
        if (is_null($this->service)) {
            $config = Config::instance()->get('app.log', []);
            $this->service = Service::instance($config);
        }

        return $this->service;
    }

    /**
     * 记录SQL信息
     *
     * @param string  $message  日志信息
     * @param boolean $trace    是否开启日志追踪
     * @return Log
     */
    public function sql($message, $trace = false)
    {
        $level = $trace ? 2 : 1;
        return $this->getService()->record($message, 'sql', $trace, $level);
    }
}
