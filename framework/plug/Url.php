<?php
namespace Laf\plug;

use FApi\Container;

/**
 * URL构建类
 *
 * @author Mon 985558837@qq.com
 * @version 2.0
 * @see 重写URL构造类，重新定义构造方法
 */
class Url
{
    /**
     * 服务容器
     *
     * @var [type]
     */
    protected $request;

    /**
     * 构造方法
     */
    public function __construct()
    {
        $this->request = Container::get('request');
    }

    /**
     * 构建URL
     *
     * @param  string  $url    URL路径
     * @param  string  $vars   传参
     * @param  boolean $domain 是否补全域名
     * @return [type]          [description]
     */
    public function build($url = '', $vars = '', $domain = false)
    {
        // $url为空是，采用当前pathinfo
        if(empty($url)){
            $url = $this->request->pathinfo();
        }

        // 判断是否包含域名,解析URL和传参
        if(false === strpos($url, '://') && 0 !== strpos($url, '/')){
            $info = parse_url($url);
            $url  = !empty($info['path']) ? $info['path'] : '';
            // 判断是否存在锚点,解析请求串
            if (isset($info['fragment'])){
                // 解析锚点
                $anchor = $info['fragment'];
                if(false !== strpos($anchor, '?')){
                    // 解析参数
                    list($anchor, $info['query']) = explode('?', $anchor, 2);
                }
            }
        }
        elseif(false !== strpos($url, '://')){
            // 存在协议头，自带domain
            $info = parse_url($url);
            $url  = $info['host'];
            $scheme = isset( $info['scheme'] ) ? $info['scheme'] : 'http';
            // 判断是否存在锚点,解析请求串
            if (isset($info['fragment'])){
                // 解析锚点
                $anchor = $info['fragment'];
                if(false !== strpos($anchor, '?')){
                    // 解析参数
                    list($anchor, $info['query']) = explode('?', $anchor, 2);
                }
            }
        }

        // 解析参数
        if(is_string($vars)){
            // aaa=1&bbb=2 转换成数组
            parse_str($vars, $vars);
        }

        // 判断是否已传入URL,且URl中携带传参, 解析传参到$vars中
        if($url && isset($info['query'])){
            // 解析地址里面参数 合并到vars
            parse_str($info['query'], $params);
            $vars = array_merge($params, $vars);
            unset($info['query']);
        }

        // 还原锚点
        $anchor = !empty($anchor) ? '#'.$anchor : '';
        // 组装传参
        if(!empty($vars)){
            $vars = http_build_query($vars);
            $url .= '?' . $vars . $anchor;
        }
        else{
            $url .= $anchor;
        }

        if(!isset($scheme)){
            // 补全baseUrl
            $url = rtrim($this->request->baseUrl(), '/') . '/' . ltrim($url, '/');
            // 判断是否需要补全域名
            if($domain === true){
                $url = $this->request->domain() . $url;
            }
        }
        else{
            $url = $scheme . '://' . $url;
        }

        return $url;
    }
}