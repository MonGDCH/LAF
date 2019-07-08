<?php

namespace Laf\provider;

use mon\env\Config;
use mon\util\Instance;
use mon\factory\Container;

/**
 * 日志处理类
 *
 * @author Mon
 * @version v2.1 修改打印日志格式
 */
class Log
{
    use Instance;

    /**
     * 日志级别
     */
    const EMERGENCY = 'emergency';
    const ALERT     = 'alert';
    const CRITICAL  = 'critical';
    const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const INFO      = 'info';
    const DEBUG     = 'debug';
    const SQL       = 'sql';
    const CACHE     = 'cache';
    const CURL      = 'curl';

    /**
     * 日志配置
     *
     * @var array
     */
    protected $config = [
        'maxSize'      => 20480000,     // 日志文件大小
        'logPath'      => '',           // 日志目录
        'rollNum'      => 3,            // 日志滚动卷数
        'logName'      => '',           // 日志名称，空则使用当前日期作为名称
    ];

    /**
     * 日志信息
     *
     * @var array
     */
    protected $log = [];

    /**
     * 初始化日志配置
     *
     * @return [type]         [description]
     */
    protected function __construct($config = [])
    {
        $config = (empty($config)) ? Config::instance()->get('log', []) : $config;
        $this->register($config);
    }

    /**
     * 注册日志配置信息
     *
     * @param  array  $config [description]
     * @return [type]         [description]
     */
    public function register($config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 获取日志信息
     *
     * @param string $type 信息类型
     * @return array
     */
    public function getLog($type = '')
    {
        return $type ? $this->log[$type] : $this->log;
    }

    /**
     * 清空日志信息
     *
     * @return $this
     */
    public function clear()
    {
        $this->log = [];
        return $this;
    }

    /**
     * 记录日志信息
     *
     * @param mixed  $msg       日志信息
     * @param string $type      日志级别
     * @param array  $context   替换内容
     * @return $this
     */
    public function record($msg, $type = 'info', array $context = [])
    {
        if (is_string($msg)) {
            $replace = [];
            foreach ($context as $key => $val) {
                $replace['{' . $key . '}'] = $val;
            }

            $msg = strtr($msg, $replace);
        }

        $this->log[$type][] = $msg;
        return $this;
    }

    /**
     * 记录日志信息
     *
     * @param string $level     日志级别
     * @param mixed  $message   日志信息
     * @param array  $context   替换内容
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        return $this->record($message, $level, $context);
    }

    /**
     * 记录emergency信息
     *
     * @param mixed  $message   日志信息
     * @param array  $context   替换内容
     * @return void
     */
    public function emergency($message, array $context = [])
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录警报信息
     *
     * @param mixed  $message   日志信息
     * @param array  $context   替换内容
     * @return void
     */
    public function alert($message, array $context = [])
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录紧急情况
     *
     * @param mixed  $message   日志信息
     * @param array  $context   替换内容
     * @return void
     */
    public function critical($message, array $context = [])
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录错误信息
     *
     * @param mixed  $message   日志信息
     * @param array  $context   替换内容
     * @return void
     */
    public function error($message, array $context = [])
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录warning信息
     *
     * @param mixed  $message   日志信息
     * @param array  $context   替换内容
     * @return void
     */
    public function warning($message, array $context = [])
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录notice信息
     *
     * @param mixed  $message   日志信息
     * @param array  $context   替换内容
     * @return void
     */
    public function notice($message, array $context = [])
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录一般信息
     *
     * @param mixed  $message   日志信息
     * @param array  $context   替换内容
     * @return void
     */
    public function info($message, array $context = [])
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录调试信息
     *
     * @param mixed  $message   日志信息
     * @param array  $context   替换内容
     * @return void
     */
    public function debug($message, array $context = [])
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录sql信息
     *
     * @param mixed  $message   日志信息
     * @param array  $context   替换内容
     * @return void
     */
    public function sql($message, array $context = [])
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录cache信息
     *
     * @param mixed  $message   日志信息
     * @param array  $context   替换内容
     * @return void
     */
    public function cache($message, array $context = [])
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 记录curl信息
     *
     * @param mixed  $message   日志信息
     * @param array  $context   替换内容
     * @return void
     */
    public function curl($message, array $context = [])
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    /**
     * 开放日志记录类型
     *
     * @param [type] $file  文件名称，__FILE__
     * @param [type] $line  文件行，__LINE__
     * @param [type] $log   日志信息
     * @param string $level 日志类型级别
     * @return void
     */
    public function oss($file, $line, $log, $level = 'info')
    {
        $message = "[{$file} => {$line}] " . $log;

        return $this->log($level, $message);
    }

    /**
     * 批量写入日志
     *
     * @return [type] [description]
     */
    public function save()
    {
        if (!empty($this->log)) {
            // 解析获取日志内容
            $log = $this->parseLog($this->log);
            $logName = empty($this->config['logName']) ? date('Ymd', $_SERVER['REQUEST_TIME']) : $this->config['logName'];
            $path = $this->config['logPath'] . DIRECTORY_SEPARATOR . $logName;

            // 分卷记录日志
            $save = Container::get('file')->subsectionFile($log, $path, $this->config['maxSize'], $this->config['rollNum']);
            // 保存成功，清空日志
            if ($save) {
                $this->clear();
            }
            return $save;
        }

        return true;
    }

    /**
     * 解析日志
     *
     * @param  [type] $logs [description]
     * @return [type]       [description]
     */
    protected function parseLog($logs)
    {
        $log = '';
        $now = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
        foreach ($logs as $type => $value) {
            $offset = "[{$now}] [{$type}] ";

            if (is_array($value)) {
                $info = '';
                foreach ($value as $data) {
                    $msg = is_string($data) ? $data : var_export($data, true);
                    $info = $info . $offset . $msg . PHP_EOL;
                }
            } else {
                $info = $offset . $value . PHP_EOL;
            }
            $log .= $info;
        }

        return $log;
    }
}
