<?php
namespace Laf\lib;

use FApi\Container;

/**
* 文件缓存类
*
* @see 第一个版本。只支持文件缓存，待后续扩展
* @author Mon <985558837@qq.com>
* @version 1.0  2017-12-01
*/
class Cache
{
	/**
	 * 配置信息
     * 
	 * @var [type]
	 */
	protected $config = [
		'expire'        => 0,
        'cache_subdir'  => true,
        'prefix'        => '',
        'path'          => '',
        'data_compress' => false,
	];

    /**
     * 缓存标志位
     *
     * @var [type]
     */
    protected $tag;

    /**
     * 缓存驱动
     *
     * @var [type]
     */
    protected $drive;

	/**
	 * 构造方法
     *
	 * @param [type] $config 缓存配置
	 */
	public function __construct(array $config = [])
	{
        // 定义配置
        $config = (empty($config)) ? Container::get('config')->get('cache', []) : $config;
        $this->config = array_merge($this->config, $config);
        if(substr($this->config['path'], -1) != DIRECTORY_SEPARATOR)
        {
            $this->config['path'] .= DIRECTORY_SEPARATOR;
        }

        // 加载驱动
        $this->drive = Container::get('file');

        // 初始化
        $this->init();
	}

	/**
	 * 初始化
     *
	 * @return [type] [description]
	 */
	public function init()
	{
		// 创建项目缓存目录
        if(!is_dir($this->config['path'])){
            $this->drive->createDir($this->config['path']);
        }
	}

	/**
	 * 取得变量的存储文件名
     *
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	protected function getCacheKey($name)
	{
		$name = md5($name);
        if($this->config['cache_subdir']){
            // 使用子目录
            $name = substr($name, 0, 2) . DIRECTORY_SEPARATOR . substr($name, 2);
        }
        if($this->config['prefix']){
            $name = $this->config['prefix'] . DIRECTORY_SEPARATOR . $name;
        }
        $filename = $this->config['path'] . $name . '.php';
        $dir      = dirname($filename);
        if (!is_dir($dir)){
            $this->drive->createDir($dir);
        }

        return $filename;
	}

	/**
	 * 获取缓存内容
     *
	 * @param  [type] $name    [description]
	 * @param  string $default [description]
	 * @return [type]          [description]
	 */
	public function get($name, $default = false)
	{
		$filename = $this->getCacheKey($name);
        if(!is_file($filename)){
            return $default;
        }
        $content = file_get_contents($filename);
        if(false !== $content){
            $expire = (int) substr($content, 8, 12);
            if(0 != $expire && $_SERVER['REQUEST_TIME'] > filemtime($filename) + $expire){
                //缓存过期删除缓存文件
                $this->drive->rm($filename);
                return $default;
            }
            $content = substr($content, 20, -3);
            if($this->config['data_compress'] && function_exists('gzcompress')){
                //启用数据压缩
                $content = gzuncompress($content);
            }

            $content = unserialize($content);
            return $content;
        }
        else{
            return $default;
        }
	}

	/**
     * 写入缓存
     *
     * @access public
     * @param string    $name 缓存变量名
     * @param mixed     $value  存储数据
     * @param int       $expire  有效时间 0为永久
     * @return boolean
     */
    public function set($name, $value, $expire = null)
    {
        if(is_null($expire)){
            $expire = $this->config['expire'];
        }
        $filename = $this->getCacheKey($name);
        if($this->tag && !is_file($filename)){
            $first = true;
        }
        $data = serialize($value);
        if($this->config['data_compress'] && function_exists('gzcompress')){
            //数据压缩
            $data = gzcompress($data, 3);
        }
        $data   = "<?php\n//" . sprintf('%012d', $expire) . $data . "\n?>";
        $result = $this->drive->createFile($data, $filename, false);
        if($result){
            isset($first) && $this->setTagItem($filename);
            clearstatcache();
            return true;
        }
        else{
            return false;
        }
    }

	/**
	 * 是否存在缓存
     *
	 * @param  [type]  $name [description]
	 * @return boolean       [description]
	 */
	public function has($name)
	{
		return $this->get($name) ? true : false;
	}

	/**
     * 删除缓存
     *
     * @param string $name 缓存变量名
     * @return boolean
     */
    public function rm($name)
    {
        return $this->drive->rm($this->getCacheKey($name));
    }

    /**
     * 清除缓存
     *
     * @access public
     * @return boolean
     */
    public function clear()
    {
        $files = (array) glob($this->config['path'] . ($this->config['prefix'] ? $this->config['prefix'] . DIRECTORY_SEPARATOR : '') . '*');
        foreach($files as $path)
        {
            if(is_dir($path)){
                array_map('unlink', glob($path . '/*.php'));
                rmdir($path);
            }
            else{
                unlink($path);
            }
        }
        return true;
    }

    /**
     * 缓存标签
     *
     * @param string        $name 标签名
     * @param string|array  $keys 缓存标识
     * @param bool          $overlay 是否覆盖
     * @return $this
     */
    public function tag($name, $keys = null, $overlay = false)
    {
        if (is_null($name)) {

        } elseif (is_null($keys)) {
            $this->tag = $name;
        } else {
            $key = 'tag_' . md5($name);
            if (is_string($keys)) {
                $keys = explode(',', $keys);
            }
            $keys = array_map([$this, 'getCacheKey'], $keys);
            if ($overlay) {
                $value = $keys;
            } else {
                $value = array_unique(array_merge($this->getTagItem($name), $keys));
            }
            $this->set($key, implode(',', $value), 0);
        }
        return $this;
    }

    /**
     * 更新标签
     *
     * @param string $name 缓存标识
     * @return void
     */
    protected function setTagItem($name)
    {
        if ($this->tag) {
            $key       = 'tag_' . md5($this->tag);
            $this->tag = null;
            if ($this->has($key)) {
                $value   = explode(',', $this->get($key));
                $value[] = $name;
                $value   = implode(',', array_unique($value));
            } else {
                $value = $name;
            }
            $this->set($key, $value, 0);
        }
    }

    /**
     * 获取标签包含的缓存标识
     * @access public
     * @param string $tag 缓存标签
     * @return array
     */
    protected function getTagItem($tag)
    {
        $key   = 'tag_' . md5($tag);
        $value = $this->get($key);
        if ($value) {
            return array_filter(explode(',', $value));
        } else {
            return [];
        }
    }
}