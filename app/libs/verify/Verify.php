<?php

namespace app\libs\verify;

use Exception;
use mon\util\Instance;

/**
 * 拖拽验证码图片生成
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class Verify
{
    use Instance;

    /**
     * 合成生成验证码图片资源
     *
     * @var mixed
     */
    protected $im = null;

    /**
     * 原始背景图片资源
     *
     * @var mixed
     */
    protected $im_fullbg = null;

    /**
     * 背景图片资源
     *
     * @var mixed
     */
    protected $im_bg = null;

    /**
     * 拖拽浮块资源
     *
     * @var mixed
     */
    protected $im_slide = null;

    /**
     * X轴偏移值
     *
     * @var integer
     */
    protected $_x = 0;

    /**
     * Y轴偏移值
     *
     * @var integer
     */
    protected $_y = 0;

    /**
     * 配置信息
     *
     * @var array
     */
    protected $config = [
        // 资源根路径
        'source'        => __DIR__,
        // 背景素材
        'bg'            => ['/bg/1.png', '/bg/2.png', '/bg/3.png'],
        // 生成验证码图片宽度
        'bg_width'      => 240,
        // 生成验证码图片高度
        'bg_height'     => 150,
        // 拖拽标志宽度
        'mark_width'    => 50,
        // 拖拽标志高度
        'mark_height'   => 50,
        // 浮块图片
        'mark_img'      => '/mark/mark2.png',
        // 浮块背景图片
        'mark_bg'       => '/mark/mark.png',
        // 校验容错率，越大体验越好，越小破解难道越高
        'fault'         => 3,
        // 验证码有效时间
        'expire'        => 60,
        // 存储驱动实例，需实现get、set、del方法
        'store'         => null,
    ];

    /**
     * 设置配置新禧
     *
     * @param array $config
     * @return Verify
     */
    public function setConfig($config)
    {
        $this->config = array_merge($this->config, $config);
        return $this;
    }

    /**
     * 创建验证图片
     *
     * @return mixed
     */
    public function create($id = '', $output = true)
    {
        $this->init($id);
        $this->createSlide();
        $this->createBg();
        $this->merge();

        // 获取输出图像
        ob_start();
        imagepng($this->im);
        $content = ob_get_clean();
        // 清空图片资源
        imagedestroy($this->im);
        imagedestroy($this->im_fullbg);
        imagedestroy($this->im_bg);
        imagedestroy($this->im_slide);
        // 输出图像
        if ($output) {
            header("Content-type: image/png");
            echo $content;
        }

        return $content;
    }

    /**
     * 验证
     *
     * @param float $offset
     * @param string $id
     * @return boolean
     */
    public function check($offset, $id = '')
    {
        $codeData = $this->getCode($this->getKey($id));
        if (empty($codeData) || empty($offset)) {
            return false;
        }
        // 验证有效期
        if (time() - $codeData['time'] > $this->config['expire']) {
            $this->delCode($this->getKey($id));
            return false;
        }
        // 验证偏移值
        $ret = abs($codeData['offset_x'] - $offset) <= $this->config['fault'];
        if ($ret) {
            $this->delCode($this->getKey($id));
        }

        return $ret;
    }

    /**
     * 初始化
     *
     * @return void
     */
    protected function init($id)
    {
        // 获取随机原背景图片
        $bg = array_rand($this->config['bg']);
        $img_bg = $this->config['bg'][$bg];
        $file_bg = $this->config['source'] .  $img_bg;
        if (!file_exists($file_bg)) {
            throw new Exception('背景图片不存在！filename => ' . $file_bg);
        }

        // 获取图像信息
        $info = getimagesize($file_bg);
        $fun = 'imagecreatefrom' . image_type_to_extension($info[2], false);
        // 读取图片
        // $this->im_fullbg = imagecreatefrompng($file_bg);
        $this->im_fullbg = call_user_func($fun, $file_bg);
        // 创建新图像
        $img = imagecreatetruecolor($this->config['bg_width'], $this->config['bg_height']);
        // 调整默认颜色
        $color = imagecolorallocatealpha($img, 255, 255, 255, 127);
        imagefill($img, 0, 0, $color);
        // 裁剪
        imagecopyresampled($img, $this->im_fullbg, 0, 0, 0, 0, $this->config['bg_width'], $this->config['bg_height'], $info[0], $info[1]);
        // 销毁原图
        imagedestroy($this->im_fullbg);
        // 设置新图像
        $this->im_fullbg = $img;
        // 按配置大小截取图片
        $this->im_bg = imagecreatetruecolor($this->config['bg_width'], $this->config['bg_height']);
        imagecopy($this->im_bg, $this->im_fullbg, 0, 0, 0, 0, $this->config['bg_width'], $this->config['bg_height']);

        // 生成浮块随机偏移值
        $this->_x = mt_rand(50, $this->config['bg_width'] - $this->config['mark_width'] - 1);
        $this->_y = mt_rand(0, $this->config['bg_height'] - $this->config['mark_height'] - 1);
        // 记录偏移值，用于验证
        $this->setCode($this->getKey($id), ['offset_x' => $this->_x, 'offset_y' => $this->_y, 'time' => time()]);
    }

    /**
     * 绘制浮块
     *
     * @return void
     */
    protected function createSlide()
    {
        // 创建浮块标准图句柄
        $this->im_slide = imagecreatetruecolor($this->config['mark_width'], $this->config['bg_height']);
        $file_mark = $this->config['source'] .  $this->config['mark_img'];
        if (!file_exists($file_mark)) {
            throw new Exception('浮块图片不存在！filename => ' . $file_mark);
        }

        // 获取图片信息
        $info = getimagesize($file_mark);
        // 读取图片
        $fun = 'imagecreatefrom' . image_type_to_extension($info[2], false);
        $img_mark = call_user_func($fun, $file_mark);
        // 创建新图像
        $img = imagecreatetruecolor($this->config['mark_width'], $this->config['mark_height']);
        // 调整默认颜色
        $color = imagecolorallocatealpha($img, 255, 255, 255, 127);
        imagefill($img, 0, 0, $color);
        // 裁剪
        imagecopyresampled($img, $img_mark, 0, 0, 0, 0, $this->config['mark_width'], $this->config['mark_height'], $info[0], $info[1]);
        // 销毁原图
        imagedestroy($img_mark);
        // 设置新图像
        $img_mark = $img;

        imagecopy($this->im_slide, $this->im_fullbg, 0, $this->_y, $this->_x, $this->_y, $this->config['mark_width'], $this->config['mark_height']);
        imagecopy($this->im_slide, $img_mark, 0, $this->_y, 0, 0, $this->config['mark_width'], $this->config['mark_height']);
        imagecolortransparent($this->im_slide, 0);
        imagedestroy($img_mark);
    }

    /**
     * 绘制浮块背景
     *
     * @return void
     */
    protected function createBg()
    {
        $file_mark = $this->config['source'] .  $this->config['mark_bg'];
        if (!file_exists($file_mark)) {
            throw new Exception('浮块背景图片不存在！filename => ' . $file_mark);
        }

        // 获取图片信息
        $info = getimagesize($file_mark);
        // 读取图片
        $fun = 'imagecreatefrom' . image_type_to_extension($info[2], false);
        $im = call_user_func($fun, $file_mark);
        // 创建新图像
        $img = imagecreatetruecolor($this->config['mark_width'], $this->config['mark_height']);
        // 调整默认颜色
        $color = imagecolorallocatealpha($img, 255, 255, 255, 127);
        imagefill($img, 0, 0, $color);
        // 裁剪
        imagecopyresampled($img, $im, 0, 0, 0, 0, $this->config['mark_width'], $this->config['mark_height'], $info[0], $info[1]);
        // 销毁原图
        imagedestroy($im);
        // 设置新图像
        $im = $img;

        imagecolortransparent($im, 0);
        imagecopy($this->im_bg, $im, $this->_x, $this->_y, 0, 0, $this->config['mark_width'], $this->config['mark_height']);
        imagedestroy($im);
    }

    /**
     * 合并图片
     *
     * @return void
     */
    protected function merge()
    {
        $this->im = imagecreatetruecolor($this->config['bg_width'], $this->config['bg_height'] * 3);
        imagecopy($this->im, $this->im_bg, 0, 0, 0, 0, $this->config['bg_width'], $this->config['bg_height']);
        imagecopy($this->im, $this->im_slide, 0, $this->config['bg_height'], 0, 0, $this->config['mark_width'], $this->config['bg_height']);
        imagecopy($this->im, $this->im_fullbg, 0, $this->config['bg_height'] * 2, 0, 0, $this->config['bg_width'], $this->config['bg_height']);
        imagecolortransparent($this->im, 0);
    }

    /**
     * 获取验证码存储索引
     *
     * @param string $id
     * @return string
     */
    protected function getKey($id)
    {
        return 'mon_verify_' . $id;
    }

    /**
     * 记录数据到session或其他存储中
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    protected function setCode($key, $value)
    {
        // 判断是否存在store驱动
        if ($this->config['store']) {
            $this->config['store']->set($key, $value);
        } else {
            $_SESSION[$key] = $value;
        }
    }

    /**
     * 获取记录的值
     *
     * @param string $key
     * @return mixed
     */
    protected function getCode($key)
    {
        // 判断是否存在store驱动
        if ($this->config['store']) {
            return $this->config['store']->get($key, null);
        }
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    /**
     * 删除
     *
     * @param string $key
     * @return void
     */
    protected function delCode($key)
    {
        // 判断是否存在store驱动
        if ($this->config['store']) {
            $this->config['store']->del($key);
        } else {
            $_SESSION[$key] = null;
            unset($_SESSION[$key]);
        }
    }
}
