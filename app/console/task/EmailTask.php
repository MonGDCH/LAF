<?php

namespace app\console\task;

use Laf\Log;
use mon\util\Instance;
use app\service\MailerService;

/**
 * 邮件相关异步事件处理
 */
class EmailTask
{
    use Instance;

    /**
     * 业务出口
     *
     * @param array $param
     * @return void
     */
    public function handle($param)
    {
        Log::instance()->record(var_export($param, true));
        if (isset($param['type'])) {
            switch ($param['type']) {
                case 'refresh':
                    // 刷新设置邮件默认配置
                    $config = isset($param['config']) ? $param['config'] : [];
                    MailerService::instance()->setConfig($config);
                    Log::instance()->info('更新邮件配置！config => ' . var_export($config, true));
                    unset($config);
                    break;
                case 'simple':
                    // 发送简洁的邮件
                    $config = isset($param['config']) ? $param['config'] : [];
                    $to = !is_array($param['to']) ? [$param['to']] : $param['to'];
                    $send = MailerService::instance()->send($param['title'], $param['content'], $to, [], [], $config);
                    if (!$send) {
                        Log::instance()->error('邮件发送失败！错误信息：' . MailerService::instance()->getError());
                        return false;
                    }
                    Log::instance()->info('邮件发送成功！');
                    // 清除数据
                    unset($title, $content, $config, $to);
                    break;
                default:
                    Log::instance()->warning('不支持的邮件发送类型');
                    break;
            }
        }
        unset($param);
        Log::instance()->save();
    }
}
