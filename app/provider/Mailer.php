<?php

namespace app\provider;

use mon\env\Config;
use mon\util\Instance;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * 邮件工具
 * 
 * @require phpmailer/phpmailer
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class Mailer
{
    use Instance;

    /**
     * 邮箱配置信息
     *
     * @var array
     */
    protected $config = [];

    /**
     * 错误信息
     *
     * @var mixed
     */
    protected $error;

    /**
     * 构造方法
     *
     * @param array $config 配置信息
     */
    public function __construct(array $config = [])
    {
        if (empty($config)) {
            $config = Config::instance()->get('email');
        }

        $this->config = array_merge($this->config, $config);
    }

    /**
     * 设置配置信息
     *
     * @param array $config 配置信息
     * @return Mailer
     */
    public function setConfig(array $config)
    {
        $this->config = array_merge($this->config, $config);
        return $this;
    }

    /**
     * 获取配置信息
     *
     * @return array 配置信息
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * 获取错误信息
     *
     * @return mixed
     */
    public function getError()
    {
        $error = $this->error;
        $this->error = null;
        return $error;
    }

    /**
     * 发送邮件
     *
     * @param string $title     邮件标题
     * @param string $content   邮件内容
     * @param array $to         接收人
     * @param array $cc         抄送人
     * @param array $bcc        秘密抄送人
     * @param array $attachment 附件
     * @param array $config     独立使用配置信息
     * @return boolean
     */
    public function send($title, $content, array $to, array $cc = [], array $bcc = [], array $attachment = [], array $config = [])
    {
        $config = empty($config) ? $this->config : $config;
        $mail = new PHPMailer(true);
        try {
            // 设定邮件编码
            $mail->CharSet = "UTF-8";
            // 调试模式输出                 
            $mail->SMTPDebug = 0;
            // SMTP服务器
            $mail->isSMTP();
            $mail->Host = $config['host'];
            // 启用SMTP身份验证
            $mail->SMTPAuth = true;
            // SMTP用户名
            $mail->Username = $config['user'];
            // SMTP密码
            $mail->Password = $config['password'];
            // 启用 TLS 或者 ssl 协议         
            if ($config['ssl']) {
                $mail->SMTPSecure = 'ssl';
            }
            // 服务器端口 25 或者465 具体要看邮箱服务器支持
            $mail->Port = $config['port'];

            // 发件人
            $mail->setFrom($config['from'], $config['name']);
            // 收件人
            foreach ($to as $item) {
                if (is_array($item)) {
                    // 设置收件人，及收件人名称
                    $mail->addAddress($item['email'], $item['name']);
                } else {
                    $mail->addAddress($item);
                }
            }
            // 回复的时候回复给哪个邮箱, 建议和发件人一致
            $mail->addReplyTo($config['from'], $config['name']);

            // 抄送
            foreach ($cc as $item) {
                $mail->addCC($item);
            }
            // 密抄
            foreach ($bcc as $item) {
                $mail->addBCC($item);
            }

            // 添加附件
            foreach ($attachment as $file) {
                if (is_array($file)) {
                    // 发送附件并且重命名  
                    $mail->addAttachment($file['path'], $file['name']);
                } else {
                    $mail->addAttachment($file);
                }
            }

            // 是否以HTML文档格式发送
            $mail->isHTML(true);
            // 邮件标题
            $mail->Subject = $title;
            // 邮件内容
            $mail->Body = $content;
            // 如果邮件客户端不支持HTML则显示此内容
            $mail->AltBody = '当前邮件客户端不支持邮件内容显示，请更换客户端查看';
            // 发送邮件
            $mail->send();
            return true;
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            $this->error = $mail->ErrorInfo;
            return false;
        }
    }
}
