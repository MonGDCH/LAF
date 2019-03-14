<?php
namespace Laf\lib;

use mon\env\Config;

/**
 * Cookie操作类
 *
 * @author Mon 985558837@qq.com
 * @version v1.0
 */
class Cookie
{
	/**
	 * Cookie相关配置
     * 
	 * @var [type]
	 */
	protected $config = [
		// cookie 名称前缀
        'prefix'    => '',
        // cookie 保存时间
        'expire'    => 0,
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        'domain'    => '',
        //  cookie 启用安全传输
        'secure'    => false,
        // httponly设置
        'httponly'  => '',
        // 是否使用 setcookie
        'setcookie' => true,
	];

    /**
     * 已初始化标志位
     *
     * @var [type]
     */
	protected $init;

	/**
	 * 注册Cookie
     * 
	 * @param  array  $config [description]
	 * @return [type]         [description]
	 */
	public function register(array $config = [])
	{
		$config = empty($config) ? Config::instance()->get('cookie') : $config;
		$this->config = array_merge($this->config, array_change_key_case($config));
		if (!empty($this->config['httponly'])) {
            ini_set('session.cookie_httponly', 1);
        }

        $this->init = true;
	}

	/**
	 * 设置获取cookie前缀
     * 
	 * @param  string $prefix [description]
	 * @return [type]         [description]
	 */
	public function prefix($prefix = '')
	{
		if(empty($prefix)){
			return $this->config['prefix'];
		}

		$this->config['prefix'] = $prefix;
	}

	/**
	 * 设置cookie
     * 
	 * @param [type] $key    键
	 * @param string $value  值
	 * @param [type] $option 重新定义的配置，必须为数组
	 */
	public function set($key, $value = '', $option = null)
	{
		!isset($this->init) && $this->register();

		// 参数设置(会覆盖黙认设置)
        if (!is_null($option) && is_array($option)) {
            $config = array_merge($this->config, array_change_key_case($option));
        }
        else {
            $config = $this->config;
        }
        $name = $config['prefix'] . $key;
        // 设置cookie
        if (is_array($value)) {
            array_walk_recursive($value, [$this, 'jsonFormatProtect'], 'encode');
            $value = 'lmf:' . json_encode($value);
        }
        $expire = !empty($config['expire']) ? $_SERVER['REQUEST_TIME'] + intval($config['expire']) : 0;
        if ($config['setcookie']) {
            setcookie($name, $value, $expire, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
        }
        $_COOKIE[$name] = $value;
	}

	/**
     * 永久保存Cookie数据
     * 
     * @param string $name  cookie名称
     * @param mixed  $value cookie值
     * @param mixed  $option 可选参数 可能会是 null|integer|string
     * @return void
     */
    public function forever($name, $value = '', $option = null)
    {
        if (is_null($option) || !is_array($option)) {
            $option = [];
        }
        $option['expire'] = 315360000;
        $this->set($name, $value, $option);
    }

    /**
     * 判断Cookie数据
     * 
     * @param string        $name cookie名称
     * @param string|null   $prefix cookie前缀
     * @return bool
     */
    public function has($name, $prefix = null)
    {
        !isset($this->init) && $this->register();

        $prefix = !is_null($prefix) ? $prefix : $this->config['prefix'];
        $name   = $prefix . $name;
        return isset($_COOKIE[$name]);
    }

    /**
     * 获取cookie值
     * 
     * @param  [type] $key     [description]
     * @param  [type] $default [description]
     * @param  [type] $prefix  [description]
     * @return [type]          [description]
     */
    public function get($key, $default = null, $prefix = null)
    {
    	!isset($this->init) && $this->register();

    	$prefix = !is_null($prefix) ? $prefix : $this->config['prefix'];
    	$name = $prefix . $key;

    	if($key == ''){
    		// 获取全部
    		if ($prefix) {
                $value = [];
                foreach ($_COOKIE as $k => $val)
                {
                    if (0 === strpos($k, $prefix)) {
                        $value[$k] = $val;
                    }
                }
            }
            else {
                $value = $_COOKIE;
            }

    	}
        elseif(isset($_COOKIE[ $name ])){
    		$value = $_COOKIE[$name];
    		// 判断是否需要转换数据
    		if (0 === strpos($value, 'lmf:')) {
                $value = substr($value, 4);
                $value = json_decode($value, true);
                array_walk_recursive($value, [$this, 'jsonFormatProtect'], 'decode');
            }
    	}
        else{
    		$value = null;
    	}

    	return ( is_null($value) || empty($value) ) ? $default : $value;
    }

    /**
     * 删除cookie
     * 
     * @param  [type] $key    [description]
     * @param  [type] $prefix [description]
     * @return [type]         [description]
     */
    public function del($key, $prefix = null)
    {
    	!isset($this->init) && $this->register();
    	$config = $this->config;
        $prefix = !is_null($prefix) ? $prefix : $config['prefix'];
        $name   = $prefix . $key;
        if ($config['setcookie']) {
            setcookie($name, '', $_SERVER['REQUEST_TIME'] - 3600, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
        }
        // 删除指定cookie
        unset($_COOKIE[$name]);
    }

    /**
     * 清空所有cookie
     * 
     * @return [type] [description]
     */
    public function clear($prefix = null)
    {
    	if(empty($_COOKIE)) return;

    	!isset($this->init) && $this->register();

    	// 要删除的cookie前缀，不指定则删除config设置的指定前缀
        $config = $this->config;
        $prefix = !is_null($prefix) ? $prefix : $config['prefix'];
        if ($prefix) {
            // 如果前缀为空字符串将不作处理直接返回
            foreach ($_COOKIE as $key => $val)
            {
                if (0 === strpos($key, $prefix)) {
                    if ($config['setcookie']) {
                        setcookie($key, '', $_SERVER['REQUEST_TIME'] - 3600, $config['path'], $config['domain'], $config['secure'], $config['httponly']);
                    }
                    unset($_COOKIE[$key]);
                }
            }
        }

        return;
    }

	/**
	 * 数组对象的json格式切换
     * 
	 * @param  [type] &$val [description]
	 * @param  [type] $key  [description]
	 * @param  string $type encode || decode
	 * @return [type]       [description]
	 */
	private function jsonFormatProtect(&$val, $key, $type = 'encode')
	{
		if (!empty($val) && true !== $val) {
            $val = 'decode' == $type ? urldecode($val) : urlencode($val);
        }
	}

}