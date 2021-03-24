<?php

namespace Laf\command;

use mon\console\Command;

/**
 * 启动内置服务
 */
class Server extends Command
{
    /**
     * 默认IP
     *
     * @var string
     */
    protected $ip = '127.0.0.1';

    /**
     * 默认端口
     *
     * @var string
     */
    protected $port = '8088';

    /**
     * 默认入口文件
     *
     * @var string
     */
    protected $entry = 'index.php';

    /**
     * 执行指令
     *
     * @param  Input  $in  输入实例
     * @param  Output $out 输出实例
     * @return integer
     */
    public function execute($in, $out)
    {
        $out->write('start LAF server...');
        $out->write('format: php LAF server [ip:' . $this->ip . '] [port:' . $this->port . '] [entry:' . $this->entry . ']');
        $args = $in->getArgs();
        $ip = isset($args[0]) ? $args[0] : $this->ip;
        $port = isset($args[1]) ? $args[1] : $this->port;
        $entry = isset($args[2]) ? $args[2] : $this->entry;
        $root = ROOT_PATH . DIRECTORY_SEPARATOR . 'public';
        // 运行的PHP内置指令
        $command = sprintf(
            'php -S %s:%d -t %s %s',
            $ip,
            $port,
            escapeshellarg($root),
            escapeshellarg($root . DIRECTORY_SEPARATOR . $entry)
        );

        $out->write("server runing {$ip}:{$port}");
        $out->write('You can exit with <info>`CTRL-C`</info>');
        $out->write(sprintf('Document root is: %s', $root));
        passthru($command);
    }
}
