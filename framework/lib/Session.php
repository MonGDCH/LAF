<?php
namespace Laf\lib;

use mon\env\Config;

/**
 * Session辅助类
 *
 * @author Mon <985558837@qq.com>
 * @version v2.0 2017-11-29
 */

class Session
{
    /**
     * session前缀空间
     *
     * @var string
     */
    protected $prefix = '';

    /**
     * 标记初始化
     *
     * @var null
     */
    protected $init = null;

    /**
     * 注册初始化session
     *
     * @return [type] [description]
     */
    public function register($config = [])
    {
        if(empty($config)){
            $config = Config::instance()->get('session');
        }
        $isDoStart = false;
        // 判断是否在php.ini中开启是否已开启session
        if(PHP_SESSION_ACTIVE != session_status()){
            // 关闭php.ini的自动开启
            ini_set('session.auto_start', 0);
            $isDoStart = true;
        }
        // 设置session前缀
        if(isset($config['prefix']) && ($this->prefix === '' || $this->prefix === null)){
            $this->prefix = $config['prefix'];
        }
        // 设置session有效期
        if(isset($config['expire']) && !empty($config['expire'])){
            ini_set('session.gc_maxlifetime', $config['expire']);
            ini_set('session.cookie_lifetime', $config['expire']);
        }
        // session安全传输
        if(isset($config['secure']) && !empty($config['secure'])){
            ini_set('session.cookie_secure', $config['secure']);
        }
        // httponly设置
        if(isset($config['httponly']) && !empty($config['httponly'])){
            ini_set('session.cookie_httponly', $config['httponly']);
        }
        // 初始化
        if($isDoStart){
            session_start();
            $this->init = true;
        }
        else{
            $this->init = false;
        }
    }

    /**
     * session自动启动或者初始化
     *
     * @return [type] [description]
     */
    public function bootstrap()
    {
        if (is_null($this->init)){
            $this->register();
        }
        elseif(false === $this->init){
            if(PHP_SESSION_ACTIVE != session_status()){
                session_start();
            }
            $this->init = true;
        }
    }

    /**
     * 设置或者获取session前缀
     *
     * @param  string $prefix [description]
     * @return [type]         [description]
     */
    public function prefix($prefix = '')
    {
        if(empty($prefix) && !is_null($prefix)){
            return $this->prefix;
        }
        else{
            $this->prefix = $prefix;
        }
    }

    /**
     * 设置session
     *
     * @param [type] $key    [description]
     * @param string $value  [description]
     * @param string $prefix [description]
     */
    public function set($key, $value = '', $prefix = null)
    {
        empty($this->init) && $this->bootstrap();
        $prefix = !is_null($prefix) ? $prefix : $this->prefix;

        if(strpos($key, '.')){
            // 二维数组赋值
            list($name1, $name2) = explode('.', $key);

            if(empty($prefix)){
                $_SESSION[$name1][$name2] = $value;
            }
            else{
                // 前缀封装
                $_SESSION[$prefix][$name1][$name2] = $value;
            }

        }
        else{

            if(empty($prefix)){
                $_SESSION[$key] = $value;
            }
            else{
                $_SESSION[$prefix][$key] = $value;
            }

        }
    }

    /**
     * 判断session是否存在
     *
     * @param  [type]  $key    [description]
     * @param  [type]  $prefix [description]
     * @return boolean         [description]
     */
    public function has($key, $prefix = null)
    {
        empty($this->init) && $this->bootstrap();
        $prefix = !is_null($prefix) ? $prefix : $this->prefix;

        if(strpos($key, '.')){
            // 二维数组赋值
            list($name1, $name2) = explode('.', $key);

            if(empty($prefix)){
                return isset( $_SESSION[$name1][$name2] );
            }
            else{
                // 前缀封装
                return isset( $_SESSION[$prefix][$name1][$name2] );
            }
        }
        else{
            if(empty($prefix)){
                return isset( $_SESSION[$key] );
            }
            else{
                return isset( $_SESSION[$prefix][$key] );
            }
        }
    }

    /**
     * 获取session
     *
     * @param  [type] $key    [description]
     * @param  string $prefix [description]
     * @return [type]         [description]
     */
    public function get($key, $default = null, $prefix = null)
    {
        empty($this->init) && $this->bootstrap();
        $prefix = !is_null($prefix) ? $prefix : $this->prefix;

        if(strpos($key, '.')){
            // 二维数组赋值
            list($name1, $name2) = explode('.', $key);
            if(empty($prefix)){
                return isset( $_SESSION[$name1][$name2] ) ? $_SESSION[$name1][$name2] : $default;
            }
            return isset( $_SESSION[$prefix][$name1][$name2] ) ? $_SESSION[$prefix][$name1][$name2] : $default;
        }
        else{
            if(empty($prefix)){
                return isset( $_SESSION[$key] ) ? $_SESSION[$key] : $default;
            }
            return isset( $_SESSION[$prefix][$key] ) ? $_SESSION[$prefix][$key] : $default;
        }
    }

    /**
     * 清空所有session
     *
     * @return [type] [description]
     */
    public function clear($prefix = null)
    {
        empty($this->init) && $this->bootstrap();
        $prefix = !is_null($prefix) ? $prefix : $this->prefix;

        if(empty($prefix)){
            $_SESSION = null;
            unset($_SESSION);
        }
        else{
            $_SESSION[$prefix] = null;
            unset($_SESSION[$prefix]);
        }
    }

    /**
     * 删除某个session
     *
     * @param  [type] $key    [description]
     * @param  [type] $prefix [description]
     * @return [type]         [description]
     */
    public function del($key, $prefix = null)
    {
        empty($this->init) && $this->bootstrap();
        $prefix = !is_null($prefix) ? $prefix : $this->prefix;

        if(strpos($key, '.')){
            // 二维数组赋值
            list($name1, $name2) = explode('.', $key);

            if(empty($prefix)){
                $_SESSION[$name1][$name2] = null;
                unset($_SESSION[$name1][$name2]);
            }
            else{
                $_SESSION[$prefix][$name1][$name2] = null;
                unset($_SESSION[$prefix][$name1][$name2]);
            }
        }
        else{
            if(empty($prefix)){
                $_SESSION[$key] = null;
                unset($_SESSION[$key]);
            }
            else{
                $_SESSION[$prefix][$key] = null;
                unset($_SESSION[$prefix][$key]);
            }
        }
    }

    /**
     * 重启session
     * 注意: 会重新生成session_id
     *
     * @return [type] [description]
     */
    public function start()
    {
        session_start();
        $this->init = true;
    }

    /**
     * 销毁session
     *
     * @return void
     */
    public function destroy()
    {
        if(!empty($_SESSION)){
            $_SESSION = [];
        }
        session_unset();
        session_destroy();
        $this->init = null;
    }

    /**
     * 重新生成session_id
     *
     * @param bool $delete 是否删除关联会话文件
     * @return void
     */
    private function regenerate($delete = false)
    {
        session_regenerate_id($delete);
    }

    /**
     * 暂停session
     *
     * @return void
     */
    public function pause()
    {
        // 暂停session
        session_write_close();
        $this->init = false;
    }

}