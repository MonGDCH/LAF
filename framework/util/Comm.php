<?php
namespace Laf\util;

/**
* 公共赋值
*
* @author Mon
* @version v1.0
*/
class Comm
{
	/**
     * 调试方法(浏览器友好处理)
     *
     * @param mixed $var 变量
     * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
     * @param string $label 标签 默认为空
     * @param boolean $strict 是否严谨 默认为true
     * @return void|string
     */
    public static function debug($var, $echo = true, $label = null, $flags = ENT_SUBSTITUTE)
    {
        $label = (null === $label) ? '' : rtrim($label) . ':';
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
        if( PHP_SAPI == 'cli' || PHP_SAPI == 'cli-server' ){
            // CLI模式
            $output = PHP_EOL . $label . $output . PHP_EOL;
        }
        else{
            if(!extension_loaded('xdebug')){
                $output = htmlspecialchars($output, $flags);
            }
            $output = '<pre>' . $label . $output . '</pre>';
        }
        if($echo){
            echo($output);
            return;
        }
        else{
            return $output;
        }
    }

    /**
     * UTF8数据转GBK格式
     *
     * @param  [type] $data [description]
     * @return [type]        [description]
     */
    public static function utf8ToGbk($data)
    {
        $result = self::iconv_recursion($data, 'GBK', 'UTF-8');
        return $result;
    }

    /**
     * GBK数据转UTF8格式
     *
     * @param  [type] $data [description]
     * @return [type]        [description]
     */
    public static function gbkToUtf8($data)
    {
        $result = self::iconv_recursion($data, 'UTF-8', 'GBK');
        return $result;
    }

    /**
     * 递归转换字符集
     *
     * @param  string $data 要转换的数据
     * @param  string $out_charset 输出编码
     * @param  string $in_charset 输入编码
     * @return mixed
     */
    public static function iconv_recursion($data, $out_charset, $in_charset)
    {
        switch(gettype($data))
        {
            case 'integer':
            case 'boolean':
            case 'float':
            case 'double':
            case 'NULL':
                return $data;
            case 'string':
                if( empty($data) || is_numeric($data) ){
                    return $data;
                }

                $data = mb_convert_encoding( $data, $out_charset, $in_charset );
                return $data;
            case 'object':
                $vars = array_keys(get_object_vars($data));
                foreach($vars as $key)
                {
                    $data->$key =  iconv_recursion($data->$key, $out_charset, $in_charset);
                }
                return $data;
            case 'array':
                foreach($data as $k => $v)
                {
                    $data[iconv_recursion($k, $out_charset, $in_charset)] =  iconv_recursion($v, $out_charset, $in_charset);
                }
                return $data;
            default:
                return $data;
        }
    }

    /**
     * 获取余数
     *
     * @param $bn 被除数
     * @param $sn 除数
     * @return int 余
     */
    public static function Kmod($bn, $sn)
    {
        $mod = intval(fmod(floatval($bn), $sn));
        return abs($mod);
    }

    /**
     * 返回正数的ip2long值
     *
     * @param  [type] $ip ip
     * @return [type]     [description]
     */
    public static function ip2long_positive($ip)
    {
        return sprintf("%u", ip2long($ip));
    }

    /**
     * 判断是否为微信浏览器发起的请求
     *
     * @return boolean [description]
     */
    public static function is_wx()
    {
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false){
            return true;
        }

        return false;
    }

    /**
     * 判断是否为安卓发起的请求
     *
     * @return boolean [description]
     */
    public static function is_android()
    {
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false){
            return true;
        }

        return false;
    }

    /**
     * 判断是否为苹果发起的请求
     *
     * @return boolean [description]
     */
    public static function is_ios()
    {
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false){
            return true;
        }

        return false;
    }

    /**
     * XML转数组
     *
     * @param  [type] $xml [description]
     * @return [type]      [description]
     */
    public static function xml2array($xml)
    {
        $p = xml_parser_create();
        xml_parse_into_struct($p, $xml, $vals, $index);
        xml_parser_free($p);
        $data = [];
        foreach ($index as $key=>$value)
        {
            if(strtolower($key) == 'xml'){
                continue;
            }
            $tag = $vals[$value[0]]['tag'];
            $value = $vals[$value[0]]['value'];
            $data[$tag] = $value;
        }
        return $data;
    }

    /**
     * 导出CSV格式文件
     *
     * @param  string $filename  导出文件名
     * @param  array  $title     表格标题列表(生成："序号,姓名,性别,年龄\n")
     * @param  array  $titleKey  表格标题列表对应键名(注意：对应title排序)
     * @param  array  $data      导出数据
     * @return file
     */
    public static function exportCsv($filename, $title, $titleKey = array(), $data = array())
    {
        // 清空之前的输出
        ob_get_contents() && ob_end_clean();

        // 获取标题
        $title  = implode(",", $title) . "\n";
        $str    = @iconv('utf-8','gbk',$title); // 中文转码GBK
        $len    = count($titleKey);

        // 遍历二维数组获取需要生成的数据
        foreach($data as $key => $value)
        {
            // 遍历键列表获取对应数据中的键值
            for($i = 0; $i < $len; $i++)
            {
                $val = @iconv('utf-8','gbk',$value[$titleKey[$i]]);

                // 判断是否为最后一列数据
                if($i == ($len - 1)){
                    $str .= $val . "\n";
                }
                else{
                    $str .= $val . ",";
                }
            }
        }

        // 输出头信息
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=".$filename . ".csv");
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        header("Content-Length: " . strlen($str));
        header("Content-Transfer-Encoding: binary");
        // 输出文件
        echo $str;
    }

    /**
     * 字符串转数组
     *
     * @param  string $str 入参，待转换的字符串
     * @return 字符数组
     */
    public static function strToMap($str)
    {
        $str = trim($str);
        $infoMap = array();
        $strArr = explode("&",$str);
        for($i=0; $i<count($strArr); $i++)
        {
            $infoArr = explode("=",$strArr[$i]);
            if(count($infoArr) != 2){
                continue;
            }
            $infoMap[$infoArr[0]] = $infoArr[1];
        }
        return $infoMap;
    }

    /**
     * 数组转字符串
     *
     * @param  array $map 入参，待转换的数组
     * @return 字符串
     */
    public static function mapToStr($map)
    {
        $str = "";
        if(!empty($map)){
            foreach($map as $k => $v)
            {
                $str .= "&".$k."=".$v;
            }
        }

        return $str;
    }
}