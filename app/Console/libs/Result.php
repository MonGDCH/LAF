<?php

namespace app\console\libs;

use mon\util\Instance;

/**
 * 结果集处理工具
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class Result
{
    use Instance;

    /**
     * 操作成功
     */
    const SUCCESS = 200;

    /**
     * 链接成功
     */
    const CONNECT = 210;

    /**
     * 心跳响应
     */
    const PONG = 220;

    /**
     * 参数错误
     */
    const ERR_PARAMS_FAILD = 400;

    /**
     * 未传递cmd指令参数
     * 
     * @var integer
     */
    const ERR_CMD_NOTFOUND = 700;

    /**
     * CMD指令未识别
     */
    const ERR_CMD_UNRECOGNIZED = 701;

    /**
     * 私有化构造方法
     */
    protected function __construct()
    {
    }

    /**
     * 返回信息
     *
     * @param integer $code     状态码
     * @param array $data       结果集
     * @param boolean $tojson   是否转为json格式
     * @return array
     */
    public function data($code, $data = [], $tojson = true)
    {
        $message = $this->getMessgae($code);
        $data = [
            'code'  => $code,
            'mag'   => $message,
            'data'  => $data
        ];
        return $tojson ? json_encode($data, JSON_UNESCAPED_UNICODE) : $data;
    }

    /**
     * 获取状态码对象的提示信息
     *
     * @param integer $code 状态码
     * @return string
     */
    public function getMessgae($code)
    {
        $message = '';
        switch ($code) {
            case self::SUCCESS:
                $message = 'success';
                break;
            case self::CONNECT:
                $message = 'connect success';
                break;
            case self::PONG:
                $message = 'pong';
                break;
            case self::ERR_PARAMS_FAILD:
                $message = 'params faild';
                break;
            case self::ERR_CMD_NOTFOUND:
                $message = 'cmd not found';
                break;
            case self::ERR_CMD_UNRECOGNIZED:
                $message = 'cmd unrecognized';
                break;
        }
        return $message;
    }
}
