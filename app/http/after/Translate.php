<?php

namespace app\http\after;

use FApi\Request;
use mon\util\File;
use app\libs\HTMLNode;
use app\service\BaiduTranslateService;

/**
 * HTML多语言翻译插件(后置中间件)
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class Translate
{
    /**
     * 配置
     *
     * @var array
     */
    protected $config = [
        // 使用的API翻译库
        'api'       => 'baidu',
        // 系统当前使用的语言
        'lang'      => 'zh',
        // 支持翻译的语言类型, 空则全部允许,['en', 'cht', 'ru', 'jp']
        'allow'     => [],
        // 前端用于控制返回语言的get请求参数名
        'lang_key'  => 'lang',
        // 语言缓存配置
        'cache'     => RUNTIME_PATH . '/cache/translate',
    ];

    /**
     * 中间件回调
     *
     * @param string $content	输出的内容
     * @param \Fapi\App $app	APP实例，返回true则继续向下执行
     * @return mixed
     */
    public function handler($result, $app)
    {
        // 获取翻译目标语言
        $toLang = Request::instance()->get($this->config['lang_key'], $this->config['lang']);
        // 目标语言与当前默认语言相同，直接返回
        if (empty($toLang) || $toLang == $this->config['lang'] || (!empty($this->config['allow']) && !in_array($toLang, $this->config['allow']))) {
            return $result;
        }
        // 中英文添加空格
        $content = $result;
        // 繁体字翻译不要加空格，保持原样式
        if (!in_array(strtolower($toLang), ['zh', 'zh-cn', 'cn', 'cht', 'tw', 'zh-tw', 'zh-hk', 'hk'])) {
            $content = preg_replace('/([\x{4e00}-\x{9fa5}]+)([A-Za-z0-9]+)/u', '${1} ${2}', $content);
            $content = preg_replace('/([A-Za-z0-9]+)([\x{4e00}-\x{9fa5}]+)/u', '${1} ${2} ', $content);
        }
        // 获取html结构内容
        $html = new HTMLNode();
        $node = $html->getNodeContent($content);
        $langData = [];
        // 获取判断缓存内容
        $cacheLangFileName = md5(Request::instance()->pathInfo() . '_' . $app->getController()) . '_' . $toLang . '.json';
        $cacheLangFile = $this->config['cache'] . '/' . $cacheLangFileName;
        // 求同存异
        if (file_exists($cacheLangFile)) {
            $langData = json_decode(file_get_contents($cacheLangFile), true);
            $langData = is_array($langData) ? $langData : [];
            // 求同
            $langData = array_intersect_key($langData, array_fill_keys($node, ''));
            // 存异
            $node = array_diff($node, array_keys($langData));
        }

        // 存在差异的语言内容，则进行翻译
        if (!empty($node)) {
            // 创建百度翻译服务
            $translate = new BaiduTranslateService();
            $translateData = $translate->translate($node, $this->config['lang'], $toLang);
            // 整理数据
            $langData = array_merge($langData, $translateData);
            $langData = $this->keyLenSort($langData);
            // 缓存数据
            File::instance()->createFile(json_encode($langData, JSON_UNESCAPED_UNICODE), $cacheLangFile, false);
        }

        // 存在替换的内容
        if (!empty($langData)) {
            $result = $html->setNodeContent($content, $langData);
        }

        return $result;
    }

    /**
     * 根据数组key长度进行排序，默认降序
     *
     * @param array $data   排序的数组
     * @param boolean $asc  排序方式
     * @return array
     */
    private function keyLenSort($data = [], $asc = false)
    {
        $keys = array_keys($data);
        for ($i = 0; $i < count($keys); $i++) {
            for ($i2 = 0; $i2 < count($keys) - $i - 1; $i2++) {
                if (($asc && strlen($keys[$i2]) > strlen($keys[$i2 + 1])) || (!$asc && strlen($keys[$i2]) < strlen($keys[$i2 + 1]))) {
                    $s = $keys[$i2 + 1];
                    $keys[$i2 + 1] = $keys[$i2];
                    $keys[$i2] = $s;
                }
            }
        }

        $keys = array_fill_keys($keys, null);
        return array_merge($keys, $data);
    }
}
