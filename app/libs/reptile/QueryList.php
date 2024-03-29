<?php

namespace app\libs\reptile;

require __DIR__ . '/phpQuery.php';

use phpQuery;

/**
 * QueryList
 *
 * 一个基于phpQuery的通用页面数据采集类
 * 
 * @author Mon <985558837@qq.com>
 * @version 3.2.2
 * @see 修正了3.2.1中存在的语法问题及BUG
 * 
 * @author 			Jaeger
 * @email 			734708094@qq.com
 * @link            http://git.oschina.net/jae/QueryList
 * @version         3.2.1
 *
 * @example 
 *
 //获取CSDN移动开发栏目下的文章列表标题
$hj = QueryList::Query('http://mobile.csdn.net/',array("title"=>array('.unit h1','text')));
print_r($hj->data);

//回调函数1
function callfun1($content,$key)
{
    return '回调函数1：'.$key.'-'.$content;
}
class HJ{
    //回调函数2
    static public function callfun2($content,$key)
    {
        return '回调函数2：'.$key.'-'.$content;
    }
}
//获取CSDN文章页下面的文章标题和内容
$url = 'http://www.csdn.net/article/2014-06-05/2820091-build-or-buy-a-mobile-game-backend';
$rules = array(
    'title'=>array('h1','text','','callfun1'),    //获取纯文本格式的标题,并调用回调函数1                   
    'summary'=>array('.summary','text','-input strong'), //获取纯文本的文章摘要，但保strong标签并去除input标签
    'content'=>array('.news_content','html','div a -.copyright'),    //获取html格式的文章内容，但过滤掉div和a标签,去除类名为copyright的元素
    'callback'=>array('HJ','callfun2')      //调用回调函数2作为全局回调函数
    );
$rang = '.left';
$hj = QueryList::Query($url,$rules,$rang);
print_r($hj->data);

//继续获取右边相关热门文章列表的标题以及链接地址
$hj->setQuery(array('title'=>array('','text'),'url'=>array('a','href')),'#con_two_2 li');
//输出数据
echo $hj->getData();
 
 */

class QueryList
{
    /**
     * 单例
     *
     * @var [type]
     */
    protected static $instances;

    /**
     * 查询的数据
     *
     * @var array
     */
    public $data;

    /**
     * html数据源
     *
     * @var string
     */
    public $html;

    /**
     * html的PHPquery数据源
     *
     * @var html
     */
    public $pqHtml;

    /**
     * 请求的URL
     *
     * @var string
     */
    private $page;

    /**
     * 输入的字符集
     *
     * @var string
     */
    private $inputEncoding;

    /**
     * html字符集类型
     *
     * @var string
     */
    private $htmlEncoding;

    /**
     * 日志驱动
     *
     * @var [type]
     */
    public static $logger;

    /**
     * 获取单例
     *
     * @return QueryList
     */
    public static function instance()
    {
        if (is_null(self::$instances)) {
            self::$instances = new static();
        }
        return self::$instances;
    }

    /**
     * 获取目标页面源码(主要用于调试)
     *
     * @param bool|true $rel
     * @return string
     */
    public  function getHtml($rel = true)
    {
        return $rel ? $this->pqHtml : $this->html;
    }

    /**
     * 获取采集结果数据
     *
     * @param callback $callback
     * @return array
     */
    public function getData($callback = null)
    {
        if (is_callable($callback)) {
            return array_map($callback, $this->data);
        }
        return $this->data;
    }

    /**
     * 查询数据，入口方法
     *
     * @param string $page            要抓取的网页URL地址(支持https);或者是html源代码
     * @param array  $rules         【选择器数组】说明：格式array("名称"=>array("选择器","类型"[,"标签过滤列表"][,"回调函数"]),.......[,"callback"=>"全局回调函数"]);
     *                               【选择器】说明:可以为任意的jQuery选择器语法
     *                               【类型】说明：值 "text" ,"html" ,"HTML标签属性" ,
     *                               【标签过滤列表】:可选，要过滤的选择器名，多个用空格隔开,当标签名前面添加减号(-)时（此时标签可以为任意的元素选择器），表示移除该标签以及标签内容，否则当【类型】值为text时表示需要保留的HTML标签，为html时表示要过滤掉的HTML标签
     *                               【回调函数】/【全局回调函数】：可选，字符串（函数名） 或 数组（array("类名","类的静态方法")），回调函数应有俩个参数，第一个参数是选择到的内容，第二个参数是选择器数组下标，回调函数会覆盖全局回调函数
     *
     * @param string $range       【块选择器】：指 先按照规则 选出 几个大块 ，然后再分别再在块里面 进行相关的选择
     * @param string $outputEncoding【输出编码格式】指要以什么编码输出(UTF-8,GB2312,.....)，防止出现乱码,如果设置为 假值 则不改变原字符串编码
     * @param string $inputEncoding 【输入编码格式】明确指定输入的页面编码格式(UTF-8,GB2312,.....)，防止出现乱码,如果设置为 假值 则自动识别
     * @param bool|false $removeHead 【是否移除页面头部区域】 乱码终极解决方案
     * @return QueryList
     */
    public function query($page, array $rules, $range = '', $inputEncoding = null, $removeHead = false)
    {
        $this->data = [];
        $this->page = $page;
        $this->html = $this->_isURL($this->page) ? $this->_request($this->page) : $this->page;
        $inputEncoding && $this->inputEncoding = $inputEncoding;
        $removeHead && $this->html = $this->_removeHead($this->html);
        $this->pqHtml = '';
        if (empty($this->html)) {
            $this->_log('The received content is empty!', 'error');
            trigger_error('The received content is empty!', E_USER_NOTICE);
        }
        //获取编码格式
        $this->htmlEncoding = $this->inputEncoding ? $this->inputEncoding : $this->_getEncode($this->html);
        // 转码HTML，统一为UTF-8
        if ($this->htmlEncoding != 'UTF-8') {
            $this->html = iconv_recursion($this->html, 'UTF-8', $this->htmlEncoding);
        }

        // $this->html = $this->_removeTags($this->html,array('script','style'));
        $this->regArr = $rules;
        $this->regRange = $range;
        $this->_getList();
        $this->_log();
        return $this;
    }

    /**
     * 重新设置选择器
     *
     * @param $rules
     * @param string $range
     * @param string $outputEncoding
     * @param string $inputEncoding
     * @param bool|false $removeHead
     * @return QueryList
     */
    public function setQuery(array $rules, $range = '', $inputEncoding = null, $removeHead = false)
    {
        return $this->query($this->html, $rules, $range, $inputEncoding, $removeHead);
    }

    /**
     * 获取解析数据
     *
     * @return void
     */
    private function _getList()
    {
        // $this->inputEncoding && phpQuery::$defaultCharset = $this->inputEncoding;
        $document = phpQuery::newDocumentHTML($this->html);
        $this->pqHtml = $document->htmlOuter();
        if (!empty($this->regRange)) {
            $robj = pq($document)->find($this->regRange);
            $i = 0;
            foreach ($robj as $item) {
                foreach ($this->regArr as $key => $reg_value) {
                    if ($key == 'callback') continue;
                    $tags = isset($reg_value[2]) ? $reg_value[2] : '';
                    $iobj = pq($item)->find($reg_value[0]);

                    switch ($reg_value[1]) {
                        case 'text':
                            $this->data[$i][$key] = $this->_allowTags(pq($iobj)->html(), $tags);
                            break;
                        case 'html':
                            $this->data[$i][$key] = $this->_stripTags(pq($iobj)->html(), $tags);
                            break;
                        default:
                            $this->data[$i][$key] = pq($iobj)->attr($reg_value[1]);
                            break;
                    }

                    if (isset($reg_value[3])) {
                        $this->data[$i][$key] = call_user_func($reg_value[3], $this->data[$i][$key], $key);
                    } else if (isset($this->regArr['callback'])) {
                        $this->data[$i][$key] = call_user_func($this->regArr['callback'], $this->data[$i][$key], $key);
                    }
                }
                //重置数组指针
                reset($this->regArr);
                $i++;
            }
        } else {
            // while (list($key, $reg_value) = each($this->regArr)) {
            foreach ($this->regArr as $key => $reg_value) {

                if ($key == 'callback') {
                    continue;
                }
                $document = phpQuery::newDocumentHTML($this->html);
                $tags = isset($reg_value[2]) ? $reg_value[2] : '';
                $lobj = pq($document)->find($reg_value[0]);
                $i = 0;
                foreach ($lobj as $item) {
                    switch ($reg_value[1]) {
                        case 'text':
                            $this->data[$i][$key] = $this->_allowTags(pq($item)->html(), $tags);
                            break;
                        case 'html':
                            $this->data[$i][$key] = $this->_stripTags(pq($item)->html(), $tags);
                            break;
                        default:
                            $this->data[$i][$key] = pq($item)->attr($reg_value[1]);
                            break;
                    }

                    if (isset($reg_value[3])) {
                        $this->data[$i][$key] = call_user_func($reg_value[3], $this->data[$i][$key], $key);
                    } else if (isset($this->regArr['callback'])) {
                        $this->data[$i][$key] = call_user_func($this->regArr['callback'], $this->data[$i][$key], $key);
                    }

                    $i++;
                }
            }
        }
        phpQuery::$documents = [];
    }

    /**
     * URL请求
     * @param $url
     * @return string
     */
    private function _request($url)
    {
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
            curl_setopt($ch, CURLOPT_REFERER, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.71 Safari/537.36');
            $result = curl_exec($ch);
            curl_close($ch);
        } elseif (version_compare(PHP_VERSION, '5.0.0') >= 0) {
            $opts = array(
                'http' => array(
                    'header' => "Referer:{$url}"
                )
            );
            $result = file_get_contents($url, false, stream_context_create($opts));
        } else {
            $result = file_get_contents($url);
        }
        return $result;
    }

    /**
     * 移除页面head区域代码
     * @param $html
     * @return mixed
     */
    private function _removeHead($html)
    {
        return preg_replace('/<head.+?>.+<\/head>/is', '<head></head>', $html);
    }

    /**
     * 获取文件编码
     * @param $string
     * @return string
     */
    private function _getEncode($string)
    {
        return mb_detect_encoding($string, array('ASCII', 'GB2312', 'GBK', 'UTF-8'));
    }

    /**
     * 转换数组值的编码格式
     * @param  array $arr           
     * @param  string $toEncoding   
     * @param  string $fromEncoding 
     * @return array                
     */
    private function _arrayConvertEncoding($arr, $toEncoding, $fromEncoding)
    {
        eval('$arr = ' . iconv($fromEncoding, $toEncoding . '//IGNORE', var_export($arr, TRUE)) . ';');
        return $arr;
    }

    /**
     * 简单的判断一下参数是否为一个URL链接
     * @param  string  $str 
     * @return boolean      
     */
    private function _isURL($str)
    {
        if (preg_match('/^http(s)?:\\/\\/.+/', $str)) {
            return true;
        }
        return false;
    }

    /**
     * 去除特定的html标签
     * @param  string $html 
     * @param  string $tags_str 多个标签名之间用空格隔开
     * @return string       
     */
    private function _stripTags($html, $tags_str)
    {
        $tagsArr = $this->_tag($tags_str);
        $html = $this->_removeTags($html, $tagsArr[1]);
        $p = [];
        foreach ($tagsArr[0] as $tag) {
            $p[] = "/(<(?:\/" . $tag . "|" . $tag . ")[^>]*>)/i";
        }
        $html = preg_replace($p, "", trim($html));
        return $html;
    }

    /**
     * 保留特定的html标签
     * @param  string $html 
     * @param  string $tags_str 多个标签名之间用空格隔开
     * @return string       
     */
    private function _allowTags($html, $tags_str)
    {
        $tagsArr = $this->_tag($tags_str);
        $html = $this->_removeTags($html, $tagsArr[1]);
        $allow = '';
        foreach ($tagsArr[0] as $tag) {
            $allow .= "<$tag> ";
        }
        return strip_tags(trim($html), $allow);
    }

    private function _tag($tags_str)
    {
        $tagArr = preg_split("/\s+/", $tags_str, -1, PREG_SPLIT_NO_EMPTY);
        $tags = array([], []);
        foreach ($tagArr as $tag) {
            if (preg_match('/-(.+)/', $tag, $arr)) {
                array_push($tags[1], $arr[1]);
            } else {
                array_push($tags[0], $tag);
            }
        }
        return $tags;
    }

    /**
     * 移除特定的html标签
     * @param  string $html 
     * @param  array  $tags 标签数组    
     * @return string       
     */
    private function _removeTags($html, $tags)
    {
        $tag_str = '';
        if (count($tags)) {
            foreach ($tags as $tag) {
                $tag_str .= $tag_str ? ',' . $tag : $tag;
            }
            // phpQuery::$defaultCharset = $this->inputEncoding ? $this->inputEncoding : $this->htmlEncoding;
            $doc = phpQuery::newDocumentHTML($html);
            pq($doc)->find($tag_str)->remove();
            $html = pq($doc)->htmlOuter();
            $doc->unloadDocument();
        }
        return $html;
    }

    /**
     * 打印日志
     * @param string $message
     * @param string $level
     */
    private function _log($message = '', $level = 'info')
    {
        if (!is_null(self::$logger)) {
            $url = $this->_isURL($this->page) ? $this->page : '[html]';
            $count = count($this->data);
            $level = empty($level) ? ($count ? 'info' : 'warning') : $level;
            $message = empty($message) ? ($count ? 'Get data successfully' : 'Get data failed') : $message;
            self::$logger->$level($message, array(
                'page' => $url,
                'count' => $count
            ));
        }
    }
}
