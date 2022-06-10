<?php

namespace app\libs;

use DOMDocument;

/**
 * 设置、获取HTML文档文本内容
 * 
 * @see 配置翻译服务，实现页面自动翻译
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class HTMLNode
{
    /**
     * 配置信息
     *
     * @var array
     */
    protected $config = [
        // 通过标签过滤
        'tagFilter'     => 'script|style|br|hr|textarea|link',
        // 通过class过滤                                                                               
        'classFilter'   => '',
        // 通过attr过滤
        'attrFilter'    => '',
        // 获取属性内容
        'attrAllow'     => 'meta:content?name=description|meta:content?name=keywords|input:placeholder|textarea:placeholder',
        // 正则表达式，根据需要替换内容
        'pattern'       => '/[a-zA-Z0-9|#|\[|\]|\-|\/|)|(|,|.|\'|"|`|:|>|<|=|{|}|_|;|!|&|?|+|-|*|\s+|、|，|©|（|）|—]/u',
        // 正则替换内容
        'replacement'   => "\n",
    ];

    /**
     * 操作返回的数据
     *
     * @var array
     */
    private $data = [];

    /**
     * 替换原内容的索引标志位
     *
     * @var array
     */
    private $keys = [];

    /**
     * 替换的内容
     *
     * @var array
     */
    private $values = [];

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
     * 获取HTML节点内容
     *
     * @param string $html  HTML文档内容
     * @param array $config 重载的配置信息
     * @return array
     */
    public function getNodeContent($html, array $config = [])
    {
        if (empty($html)) {
            return [];
        }
        if (!empty($config)) {
            $this->config = array_merge($this->config, $config);
        }

        $dom = new DOMDocument();
        // 暂时禁用标准错误，解决loadHTML函数不支持HTML5标签的缺陷
        libxml_use_internal_errors(true);
        @$dom->loadHTML($html);
        // 清除错误
        libxml_clear_errors();
        $this->parseNode($dom);

        $this->data = array_filter(array_unique($this->data));
        return array_unique($this->data);
    }

    /**
     * 设置HTML节点内容
     *
     * @param string $html   HTML文档内容
     * @param array  $data   对应的内容索引及数据
     * @param array  $config 重载的配置信息
     * @return string
     */
    public function setNodeContent($html, array $data = [], array $config = [])
    {
        $this->keys = is_array($data) ? array_keys($data) : [];
        $this->values = is_array($data) ? array_values($data) : [];
        if (!empty($config)) {
            $this->config = array_merge($this->config, $config);
        }

        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $this->parseNode($dom, true);

        $content = $dom->saveHTML($dom);
        return str_replace('%5C', "\\", $content);
    }

    /**
     * 处理DOM节点
     *
     * @param DOMDocument $node  DOM节点列表
     * @param boolean $set  重设dom值
     * @return void
     */
    protected function parseNode($node, $set = false)
    {
        if (empty($node)) {
            return false;
        }
        $nodes = gettype($node->childNodes) == 'object' ? $node->childNodes : null;
        if (empty($nodes)) {
            return false;
        }

        foreach ($nodes as $node) {
            if (!empty($node->tagName) && $node->tagName) {
                // 属性内容获取
                if ($this->config['attrAllow']) {
                    $attrs = explode('|', $this->config['attrAllow']);
                    foreach ($attrs as $attr) {
                        if (!trim($attr)) {
                            continue;
                        }
                        $attr = explode(':', $attr);
                        if (count($attr) == 2) {
                            // 标签校验
                            if ($node->tagName != trim($attr[0])) {
                                continue;
                            }
                            $attr[0] = $attr[1];
                        }

                        // 获取条件
                        $attr = explode('?', $attr[0]);
                        // 内容校验
                        $content = $node->getAttribute($attr[0]);
                        if (!trim($content)) {
                            continue;
                        }

                        if (count($attr) == 2) {
                            // 条件校验
                            $condition = explode('=', $attr[1]);
                            if (count($condition) == 2 && $node->getAttribute($condition[0]) != $condition[1]) {
                                continue;
                            }
                        }

                        if ($set) {
                            $node->setAttribute($attr[0], str_replace($this->keys, $this->values, $content));
                        } else {
                            $this->regReplace($node->getAttribute($attr[0]));
                        }
                    }
                }
            }

            // 过滤标签
            if ($this->config['tagFilter'] && !empty($node->tagName) &&  in_array($node->tagName, explode('|', $this->config['tagFilter']))) {
                continue;
            }

            if (!empty($node->tagName) && $node->tagName) {
                // 过滤class
                if ($this->config['classFilter']) {
                    $class = $node->getAttribute('class');
                    if ($class && array_intersect(explode(' ', $class), explode('|', $this->config['classFilter']))) {
                        continue;
                    }
                }

                // 属性过滤
                if ($this->config['attrFilter']) {
                    $attrs = explode('|', $this->config['attrFilter']);
                    $sign = false;
                    foreach ($attrs as $attr) {
                        $attr = explode(':', $attr);
                        if (count($attr) == 1) {
                            if ($node->getAttribute($attr[0])) {
                                $sign = true;
                                break;
                            }
                        } else {
                            if ($node->getAttribute($attr[0]) == $attr[1]) {
                                $sign = true;
                                break;
                            }
                        }
                    }
                    if ($sign) {
                        continue;
                    }
                }
            }

            if ($node->nodeType == 3) {
                if (trim($node->nodeValue)) {
                    if ($set) {
                        $node->nodeValue = str_replace($this->keys, $this->values, $node->nodeValue);
                    } else {
                        $this->regReplace($node->nodeValue);
                    }
                }
            } else {
                $this->parseNode($node, $set);
            }
        }
    }

    /**
     * 正则替换内容
     *
     * @param string $str
     * @return void
     */
    protected function regReplace($str = '')
    {
        if (!$str) {
            return false;
        } else {
            if ($this->config['pattern']) {
                $str = preg_replace($this->config['pattern'], $this->config['replacement'], $str);
                if ($str) {
                    $this->data = array_merge($this->data, explode($this->config['replacement'], $str));
                }
            } else {
                if (trim($str)) {
                    $this->data[] = trim($str);
                }
            }
        }
    }
}
