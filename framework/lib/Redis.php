<?php
namespace Laf\lib;

use Redis;
use mon\env\Config;
use BadFunctionCallException;

/**
 * Redis操作类
 *
 * @author Mon <985558837@qq.com>
 * @version 1.0 2018-05-20
 */
class Redis
{
	/**
	 * 单例实现
	 * 
	 * @var [type]
	 */
	private static $instance;

	/**
	 * redis链接实例
	 * 
	 * @var [type]
	 */
	private $handler;

	/**
	 * 配置信息
	 *
	 * @var array
	 */
	private $options = [
		'host'       => '127.0.0.1',
        'port'       => 6379,
        'password'   => '',
	];

	/**
	 * 单例实现
	 * @param  array  $options [description]
	 * @return [type]          [description]
	 */
	public static function instance($options = [])
	{
		if(is_null(self::$instance)){
			self::$instance = new self($options);
		}

		return self::$instance;
	}

	/**
     * 构造函数
     * 
     * @param array $options 配置信息
     * @access public
     */
    public function __construct($options = [])
    {
        if(!extension_loaded('redis')){
            throw new BadFunctionCallException('not support: redis');
        }
        if(empty($options)){
           $options = Config::instance()->get('redis', []);
        }
        $this->options = array_merge($this->options, $options);

        $this->handler = new Redis();
        $this->handler->connect($this->options['host'], $this->options['port']);

        if($this->options['password']){
            $this->handler->auth($this->options['password']);
        }
    }

    /**
     * 执行原生的redis操作
     * @return Redis
     */
    public function getRedis()
    {
        return $this->handler;
    }

    /************* string类型操作命令 *****************/

    /**
     * 设置一个key值
     *
     * @param [type] $key   [description]
     * @param [type] $value [description]
     */
    public function set($key, $value,$options = '')
    {
    	return $this->handler->set($key, $value,$options);
    }

    /**
     * 获取key值
     *
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public function get($key)
    {
    	return $this->handler->get($key);
    }

    /**
     * 设置一个有过期时间的key值
     * 
     * @param  [type] $key    [description]
     * @param  [type] $expire [description]
     * @param  [type] $value  [description]
     * @return [type]         [description]
     */
    public function setex($key, $expire, $value)
    {
        return $this->handler->setex($key, $expire, $value);
    }

    /**
     * 设置一个key值,如果key存在,不做任何操作
     * 
     * @param  [type] $key   [description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function setnx($key,$value)
    {
        return $this->handler->setnx($key,$value);
    }

    /**
     * 批量设置key值
     * 
     * @param  [type] $array [description]
     * @return [type]        [description]
     */
    public function mset($array)
    {
    	return $this->handler->mset($array);
    }

    /***************** hash类型操作函数 *******************/

    /**
     * 为hash表设定一个字段的值
     * 
     * @param  [type] $key   [description]
     * @param  [type] $field [description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function hSet($key, $field, $value)
    {
        return $this->handler->hSet($key, $field, $value);
    }

    /**
     * 得到hash表中一个字段的值
     * 
     * @param  [type] $key   [description]
     * @param  [type] $field [description]
     * @return [type]        [description]
     */
    public function hGet($key, $field)
    {
        return $this->handler->hGet($key, $field);
    }

    /**
     * 删除hash表中指定字段 ,支持批量删除
     *
     * @param  [type] $key   [description]
     * @param  [type] $field [description]
     * @return [type]        [description]
     */
    public function hdel($key, $field)
    {
    	$delNum = 0;
    	if(is_array($field)){
    		// 字符串，批量删除
    		foreach($field as $row)
    		{
    			$delNum += $this->handler->hDel($key, $row);
    		}
    	}
    	else{
    		// 字符串，删除单个
    		$delNum += $this->handler->hDel($key, $field);
    	}
 
        return $delNum;
    }

    /**
     * 返回hash表元素个数
     *
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public function hLen($key)
    {
        return $this->handler->hLen($key);
    }

    /**
     * 为hash表设定一个字段的值,如果字段存在，返回false
     *
     * @param  [type] $key   [description]
     * @param  [type] $field [description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function hSetNx($key, $field, $value)
    {
        return $this->handler->hSetNx($key, $field, $value);
    }

    /**
     * 为hash表多个字段设定值。
     * 
     * @param  [type] $key   [description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public function hMset($key, $value)
    {
        if(!is_array($value)){
            return false;
        }

        return $this->handler->hMset($key, $value); 
    }

    /**
     * 获取hash表多个字段值。
     * @param string $key
     * @param array|string $value string以','号分隔字段
     * @return array|bool
     */
    public function hMget($key, $field)
    {
        if(!is_array($field)){
            $field = explode(',', $field);
        }

        return $this->handler->hMget($key, $field);
    }

    /**
     * 为hash表设这累加，可以负数
     * 
     * @param string $key   key值
     * @param int $field    字段
     * @param string $value 步长
     * @return bool
     */
    public function hIncrBy($key, $field, $value)
    {
        $value = intval($value);

        return $this->handler->hIncrBy($key, $field, $value);
    }

    /**
     * 返回所有hash表的所有字段
     *
     * @param string $key
     * @return array|bool
     */
    public function hKeys($key)
    {
        return $this->handler->hKeys($key);
    }

    /**
     * 返回所有hash表的字段值，为一个索引数组
     * 
     * @param string $key
     * @return array|bool
     */
    public function hVals($key)
    {
        return $this->handler->hVals($key);
    }

    /**
     * 返回所有hash表的字段值，为一个关联数组
     * 
     * @param string $key
     * @return array|bool
     */
    public function hGetAll($key)
    {
        return $this->handler->hGetAll($key);
    }

    /********************* List队列类型操作命令 ************************/

    /**
     * 在队列尾部插入一个元素
     * 
     * @param [type] $key
     * @param [type] $value
     * 返回队列长度
     */
    public function rPush($key, $value)
    {
        return $this->handler->rPush($key, $value); 
    }
     
    /**
     * 在队列尾部插入一个元素 如果key不存在，什么也不做
     * 
     * @param [type] $key
     * @param [type] $value
     * @return 返回队列长度
     */
    public function rPushx($key, $value)
    {
        return $this->handler->rPushx($key, $value);
    }
     
    /**
     * 在队列头部插入一个元素
     * 
     * @param [type] $key
     * @param [type] $value
     * @return 返回队列长度
     */
    public function lPush($key, $value)
    {
        return $this->handler->lPush($key, $value);
    }
     
    /**
     * 在队列头插入一个元素 如果key不存在，什么也不做
     * @param [type] $key
     * @param [type] $value
     * @return 返回队列长度
     */
    public function lPushx($key, $value)
    {
        return $this->handler->lPushx($key, $value);
    }
     
    /**
     * 返回队列长度
     * 
     * @param [type] $key
     */
    public function lLen($key)
    {
        return $this->handler->lLen($key); 
    }
     
    /**
     * 返回队列指定区间的元素
     * 
     * @param [type] $key
     * @param [type] $start
     * @param [type] $end
     */
    public function lRange($key, $start, $end)
    {
        return $this->handler->lrange($key, $start, $end);
    }
     
    /**
     * 返回队列中指定索引的元素
     * 
     * @param [type] $key
     * @param [type] $index
     */
    public function lIndex($key, $index)
    {
        return $this->handler->lIndex($key, $index);
    }
     
    /**
     * 设定队列中指定index的值。
     * 
     * @param [type] $key
     * @param [type] $index
     * @param [type] $value
     */
    public function lSet($key, $index, $value)
    {
        return $this->handler->lSet($key, $index, $value);
    }
     
    /**
     * 删除值为vaule的count个元素
     * PHP-redis扩展的数据顺序与命令的顺序不太一样，不知道是不是bug
     * count>0 从尾部开始
     *  >0　从头部开始
     *  =0　删除全部
     *  
     * @param [type] $key
     * @param [type] $count
     * @param [type] $value
     */
    public function lRem($key, $count, $value)
    {
        return $this->handler->lRem($key, $value, $count);
    }
     
    /**
     * 删除并返回队列中的头元素。
     * 
     * @param [type] $key
     */
    public function lPop($key)
    {
        return $this->handler->lPop($key);
    }
     
    /**
     * 删除并返回队列中的尾元素
     * 
     * @param [type] $key
     */
    public function rPop($key)
    {
        return $this->handler->rPop($key);
    }

    /************* 无序集合操作命令 *****************/

    /**
     * 返回集合中所有元素
     *
     * @param [type] $key
     */
    public function sMembers($key)
    {
        return $this->handler->sMembers($key);
    }
     
    /**
     * 求2个集合的差集
     *
     * @param [type] $key1
     * @param [type] $key2
     */
    public function sDiff($key1, $key2)
    {
        return $this->handler->sDiff($key1, $key2);
    }
     
    /**
     * 添加集合。由于版本问题，扩展不支持批量添加。这里做了封装
     *
     * @param [type] $key
     * @param string|array $value
     * @return 增加数
     */
    public function sAdd($key, $value)
    {
        if(!is_array($value)){
            $arr = array($value);
        }
        else{
            $arr = $value;
        }

        foreach($arr as $row)
        {
            $this->handler->sAdd($key, $row);
        }
    }
     
    /**
     * 返回无序集合的元素个数
     *
     * @param [type] $key
     */
    public function scard($key)
    {
        return $this->handler->scard($key);
    }
     
    /**
     * 从集合中删除一个元素
     *
     * @param [type] $key
     * @param [type] $value
     */
    public function srem($key, $value)
    {
        return $this->handler->srem($key, $value);
    }

    /********************* sorted set有序集合类型操作命令 *********************/

    /**
     * 给当前集合添加一个元素，如果value已经存在，会更新order的值。
     * 
     * @param string $key
     * @param string $order 序号
     * @param string $value 值
     * @return bool
     */
    public function zAdd($key, $order, $value)
    {
        return $this->handler->zAdd($key, $order, $value);   
    }

    /**
     * 给$value成员的order值，增加$num,可以为负数
     * 
     * @param string $key
     * @param string $num 序号
     * @param string $value 值
     * @return 返回新的order
     */
    public function zinCry($key, $num, $value)
    {
        return $this->handler->zinCry($key, $num, $value);
    }

    /**
     * 删除值为value的元素
     * 
     * @param string $key
     * @param stirng $value
     * @return bool
     */
    public function zRem($key, $value)
    {
        return $this->handler->zRem($key,$value);
    }

    /**
     * 集合以order递增排列后，0表示第一个元素，-1表示最后一个元素
     * 
     * @param string $key
     * @param int $start
     * @param int $end
     * @return array|bool
     */
    public function zRange($key, $start, $end)
    {
        return $this->handler->zRange($key, $start, $end);
    }

    /**
     * 集合以order递减排列后，0表示第一个元素，-1表示最后一个元素
     * 
     * @param string $key
     * @param int $start
     * @param int $end
     * @return array|bool
     */
    public function zRevRange($key, $start, $end)
    {
        return $this->handler->zRevRange($key, $start, $end);
    }

    /**
     * 集合以order递增排列后，返回指定order之间的元素。
     * min和max可以是-inf和+inf　表示最大值，最小值
     * 
     * @param string $key
     * @param int $start
     * @param int $end
     * @package array $option 参数
     *     withscores=>true，表示数组下标为Order值，默认返回索引数组
     *     limit=>array(0,1) 表示从0开始，取一条记录。
     * @return array|bool
     */
    public function zRangeByScore($key, $start='-inf', $end="+inf", $option=array())
    {
        return $this->handler->zRangeByScore($key, $start, $end, $option);
    }
     
    /**
     * 集合以order递减排列后，返回指定order之间的元素。
     * min和max可以是-inf和+inf　表示最大值，最小值
     * 
     * @param string $key
     * @param int $start
     * @param int $end
     * @package array $option 参数
     *     withscores=>true，表示数组下标为Order值，默认返回索引数组
     *     limit=>array(0,1) 表示从0开始，取一条记录。
     * @return array|bool
     */
    public function zRevRangeByScore($key, $start='-inf', $end="+inf", $option=array())
    {
        return $this->handler->zRevRangeByScore($key, $start, $end, $option);
    }
     
    /**
     * 返回order值在start end之间的数量
     * 
     * @param [type] $key
     * @param [type] $start
     * @param [type] $end
     */
    public function zCount($key, $start, $end)
    {
        return $this->handler->zCount($key, $start, $end);
    }
     
    /**
     * 返回值为value的order值
     * 
     * @param [type] $key
     * @param [type] $value
     */
    public function zScore($key,$value)
    {
        return $this->handler->zScore($key, $value);
    }
     
    /**
     * 返回集合以score递增加排序后，指定成员的排序号，从0开始。
     * 
     * @param [type] $key
     * @param [type] $value
     */
    public function zRank($key, $value)
    {
        return $this->handler->zRank($key, $value);
    }
     
    /**
     * 返回集合以score递增加排序后，指定成员的排序号，从0开始。
     * 
     * @param [type] $key
     * @param [type] $value
     */
    public function zRevRank($key, $value)
    {
        return $this->handler->zRevRank($key, $value);
    }
     
    /**
     * 删除集合中，score值在start end之间的元素　包括start end
     * min和max可以是-inf和+inf　表示最大值，最小值
     * 
     * @param [type] $key
     * @param [type] $start
     * @param [type] $end
     * @return 删除成员的数量。
     */
    public function zRemRangeByScore($key, $start, $end)
    {
        return $this->handler->zRemRangeByScore($key, $start, $end);
    }
     
    /**
     * 返回集合元素个数。
     * 
     * @param [type] $key
     */
    public function zCard($key)
    {
        return $this->handler->zCard($key);
    }

    /********************* 事务的相关方法 ************************/
     
    /**
     * 监控key,就是一个或多个key添加一个乐观锁
     * 在此期间如果key的值如果发生的改变，刚不能为key设定值
     * 可以重新取得Key的值。
     * 
     * @param [type] $key
     */
    public function watch($key)
    {
        return $this->handler->watch($key);
    }
     
    /**
     * 取消当前链接对所有key的watch
     *  EXEC 命令或 DISCARD 命令先被执行了的话，那么就不需要再执行 UNWATCH 了
     */
    public function unwatch()
    {
        return $this->handler->unwatch();
    }
     
    /**
     * 开启一个事务
     * 事务的调用有两种模式Redis::MULTI和Redis::PIPELINE，
     * 默认是Redis::MULTI模式，
     * Redis::PIPELINE管道模式速度更快，但没有任何保证原子性有可能造成数据的丢失
     */
    public function multi($type = Redis::MULTI)
    {
        return $this->handler->multi($type);
    }
     
    /**
     * 执行一个事务
     * 收到 EXEC 命令后进入事务执行，事务中任意命令执行失败，其余的命令依然被执行
     */
    public function exec()
    {
        return $this->handler->exec();
    }
     
    /**
     * 回滚一个事务
     */
    public function discard()
    {
        return $this->handler->discard();
    }

    /************* 订阅操作命令 *****************/

    /**
     * 发布订阅
     *
     * @param  [type] $channel 发布的频道
     * @param  [type] $messgae 发布信息
     * @return int             订阅数
     */
    public function publish($channel, $messgae)
    {
        return $this->handler->publish($channel, $messgae);
    }


    /************* 管理操作命令 *****************/

    /**
     * 测试当前链接是不是已经失效
     * 没有失效返回+PONG
     * 失效返回false
     */
    public function ping()
    {
        return $this->handler->ping();
    }
    
    /**
     * 密码认证
     *
     * @param  [type] $auth [description]
     * @return [type]       [description]
     */
    public function auth($auth)
    {
        return $this->handler->auth($auth);
    }

    /**
     * 选择数据库
     *
     * @param int $dbId 数据库ID号
     * @return bool
     */
    public function select($dbId)
    {
        return $this->handler->select($dbId);
    }

    /**
     * 清空当前数据库
     *
     * @return bool
     */
    public function flushDB()
    {
        return $this->handler->flushDB();
    }

    /**
     * 返回当前库状态
     *
     * @return array
     */
    public function info()
    {
        return $this->handler->info();
    }
     
    /**
     * 同步保存数据到磁盘
     */
    public function save()
    {
        return $this->handler->save();
    }
     
    /**
     * 异步保存数据到磁盘
     */
    public function bgSave()
    {
        return $this->handler->bgSave();
    }
     
    /**
     * 返回最后保存到磁盘的时间
     */
    public function lastSave()
    {
        return $this->handler->lastSave();
    }
     
    /**
     * 返回key,支持*多个字符，?一个字符
     * 只有*　表示全部
     *
     * @param string $key
     * @return array
     */
    public function keys($key)
    {
        return $this->handler->keys($key);
    }
     
    /**
     * 删除指定key
     *
     * @param [type] $key
     */
    public function del($key)
    {
        return $this->handler->del($key);
    }
     
    /**
     * 判断一个key值是不是存在
     *
     * @param [type] $key
     */
    public function exists($key)
    {
        return $this->handler->exists($key);
    }
     
    /**
     * 为一个key设定过期时间 单位为秒
     *
     * @param [type] $key
     * @param [type] $expire
     */
    public function expire($key,$expire)
    {
        return $this->handler->expire($key,$expire);
    }
     
    /**
     * 返回一个key还有多久过期，单位秒
     *
     * @param [type] $key
     */
    public function ttl($key)
    {
        return $this->handler->ttl($key);
    }
     
    /**
     * 设定一个key什么时候过期，time为一个时间戳
     *
     * @param [type] $key
     * @param [type] $time
     */
    public function exprieAt($key, $time)
    {
        return $this->handler->expireAt($key,$time);
    }
     
    /**
     * 关闭服务器链接
     */
    public function close()
    {
        return $this->handler->close();
    }

    /**
     * 返回当前数据库key数量
     */
    public function dbSize()
    {
        return $this->handler->dbSize();
    }
     
    /**
     * 返回一个随机key
     */
    public function randomKey()
    {
        return $this->handler->randomKey();
    }

}
