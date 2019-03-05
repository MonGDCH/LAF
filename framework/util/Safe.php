<?php
namespace Laf\util;

/**
* 安全相关处理
*
* @author Mon 985558837@qq.com
* @version v1.0
*/
class Safe
{
	/**
     * 字符串编码过滤（中文、英文、数字不过滤，只过滤特殊字符）
     *
     * @param  [type] $src [description]
     * @return [type]      [description]
     */
    public static function encodeEX($src)
    {
        $result = '';
        $len = strlen($src);
        $encode_buf = '';
        for($i = 0; $i < $len; $i++)
        {
            $sChar = substr($src, $i, 1);
            switch($sChar)
            {
                case "~":
                case "`":
                case "!":
                case "@":
                case "#":
                case "$":
                case "%":
                case "^":
                case "&":
                case "*":
                case "(":
                case ")":
                case "-":
                case "_":
                case "+":
                case "=":
                case "{":
                case "}":
                case "[":
                case "]":
                case "|":
                case "\\":
                case ";":
                case ":":
                case "\"":
                case ",":
                case "<":
                case ">":
                case ".":
                case "?":
                case "/":
                case " ":
                case "'":
                case "\"":
                case "\n":
                case "\r":
                case "\t":
                        $encode_buf = sprintf("%%%s", bin2hex($sChar));
                        $result .= $encode_buf;
                    break;
                default:
                    $result .= $sChar;
                    break;
            }
        }

        return $result;
    }

    /**
     * 字符串解码（对应encodeEX）
     *
     * @param  [type] $src [description]
     * @return [type]      [description]
     */
    public static function decodeEX($src)
    {
        $result = '';
        $len = mb_strlen($src);
        $chDecode;
        for($i = 0; $i < $len; $i++)
        {
            $sChar = mb_substr($src,$i,1);
            if($sChar == '%' && $i < ($len - 2) && self::IsXDigit(mb_substr($src, $i+1, 1)) && self::IsXDigit(mb_substr($src, $i+2, 1))){
                $chDecode = mb_substr($src, $i+1, 2);
                $result .= pack("H*", $chDecode);
                $i += 2;
            }
            else{
                $result .= $sChar;
            }
        }

        return $result;
    }

    /**
     * 防止script里面的 XSS
     *
     * @param  [type] $str [description]
     * @return [type]      [description]
     */
    public static function jsformat($str)
    {
        $str = trim($str);
        $str = str_replace('\\s\\s', '\\s', $str);
        $str = str_replace(chr(10), '', $str);
        $str = str_replace(chr(13), '', $str);
        $str = str_replace(' ', '', $str);
        $str = str_replace('\\', '\\\\', $str);
        $str = str_replace('"', '\\"', $str);
        $str = str_replace('\\\'', '\\\\\'', $str);
        $str = str_replace("'", "\'", $str);
        $str = str_replace(">", "", $str);
        $str = str_replace("<", "", $str);
        return $str;
    }

    /**
     * 创建基于cookies的Token
     *
     * @param  String  $ticket 验证秘钥
     * @param  integer $expire Cookie生存时间
     * @return cookie&array
     */
    public static function createTicket($ticket, $ticket_title = "Mon", $expire = 3600)
    {
        // 自定义Token头
        $ticket_title = 'LAF_' . $ticket_header;
        $now    = time();
        $token  = md5($ticket_title.$now.$ticket);

        $_COOKIE['_token_']     = $token;
        $_COOKIE['_tokenTime_'] = $now;
        setcookie("_token_", $token, $now + $expire, "/");
        setcookie("_tokenTime_", $now, $now + $expire, "/");

        return array('token' => $token, 'tokenTime' => $now);
    }

    /**
     * 校验基于cookies的Token
     *
     * @param  String  $ticket      验证秘钥
     * @param  String  $token       Token值
     * @param  String  $tokenTime   Token创建时间
     * @param  boolean $destroy     是否清除Cookie
     * @param  integer $expire      Cookie生存时间
     * @return bool
     */
    public static function checkTicket($ticket, $token = null, $tokenTime = null, $ticket_title = "Mon", $destroy = true, $expire = 3600)
    {
        // 自定义Token头
        $ticket_title = 'LAF_' . $ticket_header;
        $token        = empty($token) ? $_COOKIE['_token_'] : $token;
        $tokenTime    = empty($tokenTime) ? $_COOKIE['_tokenTime_'] : $tokenTime;
        $now          = time();
        $result       = false;

        if(empty($token) || empty($tokenTime)){
            return $result;
        }

        //校验
        $check      = md5($ticket_title.$tokenTime.$ticket);
        $timeGap    = $now - $tokenTime;
        if($check == $token && $timeGap <= $expire){
            $result = true;
        }

        // 判断是否需要清空Cookie
        if($destroy){
            setcookie("_token_", "", $now - $expire, "/");
            setcookie("_tokenTime_", "", $now - $expire, "/");
        }

        return $result;
    }

    /**
     * 判断是否为16进制，由于PHP没有相关的API，所以折中处理
     *
     * @param  [type]  $src [description]
     * @return boolean      [description]
     */
    public static function isXDigit($src)
    {
        if(mb_strlen($src) < 1){
            return false;
        }
        if(($src >= '0' && $src <= '9') || ($src >= 'A' && $src <= 'F') || ($src >= 'a' && $src <= 'f')){
            return true;
        }

        return false;
    }
}