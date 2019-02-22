<?php
namespace Laf\util;

/**
* 数组处理相关
*
* @author Mon <985558837@qq.com>
* @version v1.0
*/
class Arrs
{
	/**
     * 二维数组去重(键&值不能完全相同)
     *
     * @param  array $arr    需要去重的数组
     * @return array $result
     */
    public static function array_2D_unique($arr)
    {
        foreach($arr as $v)
        {
            // 降维,将一维数组转换为用","连接的字符串.
            $v = implode(",", $v);
            $result[] = $v;
        }
        // 去掉重复的字符串,也就是重复的一维数组
        $result = array_unique($result);

        // 重组数组
        foreach($result as $k => $v)
        {
            $result[$k] = explode(",", $v);//再将拆开的数组重新组装
        }
        sort($result);

        return $result;
    }

    /**
     * 二维数组去重(值不能相同)
     *
     * @param  array $arr    需要去重的数组
     * @return array $result
     */
    public static function array_2D_value_unique($arr)
    {
        $tmp = array();
        foreach($arr as $k => $v)
        {
            //搜索$v[$key]是否在$tmp数组中存在，若存在返回true
            if(in_array($v, $tmp)){    
                unset($arr[$k]);
            }
            else{
                $tmp[] = $v;
            }
        }
        sort($arr);

        return $arr;
    }

    /**
     * 是否为关联数组
     *
     * @param  array   $array [description]
     * @return boolean        [description]
     */
    public static function isAssoc(array $array)
    {
        $keys = array_keys($array);
        return array_keys($keys) !== $keys;
    }
}