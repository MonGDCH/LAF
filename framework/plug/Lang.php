<?php
namespace Laf\plug;

use Exception;

/**
 * 多语言处理
 *
 * @author Mon <985558837@qq.com>
 * @version v1.0
 */
class Lang
{
	/**
	 * 当前语言
	 *
	 * @var string
	 */
	protected $lang = 'cn';

	/**
	 * 语言配置
	 *
	 * @var array
	 */
	protected $config = [];

	/**
	 * 设置默认语言
	 *
	 * @param [type] $lang [description]
	 */
	public function setLang($lang)
	{
		$this->lang = $lang;

		return $this;
	}

	/**
	 * 设置语言包
	 *
	 * @param array  $config [description]
	 * @param string $lang   [description]
	 */
	public function setConfig(array $config = [], $lang = '')
	{
		$lang = (empty($lang)) ? $this->lang : $lang;
		$this->config[$lang] = $config;

		return $this;
	}

	/**
	 * 加载语言包
	 *
	 * @param  string $path [description]
	 * @param  string $lang [description]
	 * @return [type]       [description]
	 */
	public function loadConfig($path, $lang)
	{
		$this->config[$lang] = include($path);

		return $this;
	}

	/**
	 * 获取对应语言描述
	 *
	 * @param  string $msg  [description]
	 * @param  string $lang [description]
	 * @return [type]       [description]
	 */
	public function get($msg, $lang = '')
	{
		$lang = (empty($lang)) ? $this->lang : $lang;
		if(!isset($this->config[$lang]) || !isset($this->config[$lang][$msg])){
			return $msg;
		}

		return $this->config[$lang][$msg];
	}
}