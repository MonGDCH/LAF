<?php

namespace app\service;

use mon\util\Network;
use mon\util\Instance;

/**
 * 百度翻译服务
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class BaiduTranslateService
{
    use Instance;

    /**
     * 配置信息
     *
     * @var array
     */
    private $config = [
        // API地址
        'api'       => 'http://api.fanyi.baidu.com/api/trans/vip/translate',
        // APPID
        'appId'     => '20201210000643550',
        // APP秘钥
        'secretKey' => 'WRhIMl55bX74XpeodSsi',
        // 一次翻译一行最多多少个字符，单位：百
        'quality'   => 5,
        // 间隔多少秒请求一次API
        'qps'       => 1,
    ];

    /**
     * 构造方法
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 翻译
     *
     * @param array  $query 要翻译的内容
     * @param string $from  当前语种
     * @param string $to    目标语种
     * @return array
     */
    public function translate(array $query, $from = 'zh', $to = 'en')
    {
        // 获取要翻译的内容
        if (empty($query)) {
            return [];
        }
        $q = array_unique($query);

        // 翻译
        $data = [];
        $s = '';
        foreach ($q as $v) {
            $s .= "\n";
            if (strlen($s . $v) >= intval($this->config['quality']) * 100) {
                $ret = $this->resultHandle($s, $from, $to);
                $data = array_merge($data, $ret);
                $s = '';
                $qps = intval($this->config['qps']) ? 1 / intval($this->config['qps']) * 1000 * 1000 : 0;
                usleep($qps);
            }
            $s .= $v;
        }

        if (!empty($s)) {
            $ret = $this->resultHandle($s, $from, $to);
            $data = array_merge($data, $ret);
        }

        return $data;
    }

    /**
     * 执行处理翻译结果
     *
     * @param string $query 要翻译的内容
     * @param string $from  当前语种
     * @param string $to    目标语种
     * @return array
     */
    protected function resultHandle($query, $from, $to)
    {
        if (empty($query)) {
            return [];
        }

        $data = [];
        $ret = $this->run($query, $from, $to);
        if (empty($ret) || (!empty($ret['error_code']) && $ret['error_code'] != 52000)) {
            // 翻译失败, 可能原因请求频繁、appid 或 key不正确、具体参考：https://fanyi-api.baidu.com/doc/21
            return [];
        } else {
            if (!empty($ret['trans_result'])) {
                foreach ($ret['trans_result'] as $k => $v) {
                    $data[$v['src']] = $v['dst'];
                }
            }
        }

        return $data;
    }

    /**
     * 执行翻译
     *
     * @param string $query 要翻译的内容
     * @param string $from  当前语种
     * @param string $to    目标语种
     * @return array
     */
    protected function run($query, $from, $to)
    {
        $args = [
            'q'     => $query,
            'appid' => $this->config['appId'],
            'salt'  => rand(10000, 99999),
            'from'  => $from,
            'to'    => $to
        ];
        $args['sign'] = $this->buildSign($query, $args['appid'], $args['salt'], $this->config['secretKey']);
        $ret = $this->sendQuery($this->config['api'], $args, 'post', 0);
        return $ret;
    }

    /**
     * 生成签名
     *
     * @param string $query     翻译内容
     * @param string $appID     APPID
     * @param string $salt      随机盐
     * @param string $secKey    secretKey
     * @return string
     */
    protected function BuildSign($query, $appID, $salt, $secKey)
    {
        $str = $appID . $query . $salt . $secKey;
        $ret = md5($str);
        return $ret;
    }

    /**
     * 发起网络请求
     *
     * @param string $url       请求URL
     * @param array $args       请求参数
     * @param string $method    请求类型
     * @param integer $timeout  超时时间
     * @param array $headers    请求头
     * @return mixed
     */
    protected function sendQuery($url, $args = [], $method = 'post', $timeout = 30, $headers = [])
    {
        $ret = false;
        // 调用免费的百度API，存在调用频率限制，这里做下失败重调处理
        $i = 0;
        while ($ret === false) {
            if ($i > 1) {
                break;
            }
            if ($i > 0) {
                $qps = intval($this->config['qps']) ? 1 / intval($this->config['qps']) * 1000 * 1000 : 0;
                usleep($qps);
            }
            $ret = Network::instance()->sendHTTP($url, $args, $method, true, $timeout, $headers);
            $i++;
        }
        return $ret;
    }
}
