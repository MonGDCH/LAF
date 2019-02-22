<?php
namespace Laf\lib;

/**
 * 验证码类
 */
class Captcha
{
	/**
	 * 随机因子
	 *
	 * @var [type]
	 */
    private $charset = [
    	'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 
    	'j', 'k', 'm', 'n', 'p', 'r', 's', 't', 
    	'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 
    	'C', 'D', 'E', 'F', 'G', 'H', 'K', 'M', 
    	'N', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 
    	'X', 'Y', 'Z', '2', '3', '4', '5', '6', 
    	'7', '8', '9'
    ];

    /**
     * 验证码
     *
     * @var array
     */
    private $code = []; 

    /**
     * 验证码长度
     *
     * @var integer
     */
    private $codelen = 4;

    /**
     * 宽度
     *
     * @var integer
     */
    private $width = 130;

    /**
     * 高度
     *
     * @var integer
     */
    private $height = 50;

    /**
     * 图形资源句柄
     *
     * @var [type]
     */
    private $img;

    /**
     * 指定的字体
     *
     * @var [type]
     */
    private $font;

    /**
     * 指定字体大小
     *
     * @var integer
     */
    private $fontsize = 20;

    /**
     * 指定字体颜色
     *
     * @var [type]
     */
    private $fontcolor;

    /**
     * 构造方法初始化
     */
    public function __construct()
    {
        $this->font = __DIR__ . '/captcha/elephant.ttf';
    }

    /**
     * 生成随机码
     *
     * @return [type] [description]
     */
    private function createCode()
    {
        $_len = count($this->charset) - 1;
        for($i = 0; $i <= $this->codelen - 1; $i++)
        {
            $this->code[] = $this->charset[mt_rand(0, $_len)];
        }
    }

    /**
     * 生成背景
     *
     * @return [type] [description]
     */
    private function createBg()
    {
        $this->img = imagecreatetruecolor($this->width, $this->height);
        $color = imagecolorallocate($this->img, mt_rand(157, 255), mt_rand(157, 255), mt_rand(157, 255));
        imagefilledrectangle($this->img, 0, $this->height, $this->width, 0, $color);
    }

    /**
     * 生成文字
     *
     * @return [type] [description]
     */
    private function createFont()
    {
        $_x = ($this->width) / ($this->codelen);
        for($i = 0; $i <= $this->codelen - 1; $i++)
        {
            $this->fontcolor = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imagettftext($this->img, $this->fontsize, mt_rand(-30, 30), $_x * $i + mt_rand(1, 5), $this->height / 1.4, $this->fontcolor, $this->font, $this->code[$i]);
        }
    }

    /**
     * 生成线条、雪花
     *
     * @return [type] [description]
     */
    private function createLine()
    {
        //线条
        for($i = 0; $i <= 5; $i++)
        {
            $color = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imageline($this->img, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $color);
        }
        //雪花
        for($i = 0; $i <= 99; $i++)
        {
            $color = imagecolorallocate($this->img, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
            imagestring($this->img, mt_rand(1, 5), mt_rand(0, $this->width), mt_rand(0, $this->height), "*", $color);
        }
    }

    /**
     * 输出
     *
     * @return [type] [description]
     */
    private function outPut()
    {
        header("Content-type:image/jpeg");
        imagepng($this->img);
        imagedestroy($this->img);
    }

    /**
     * 对外生成
     *
     * @param  string $code [description]
     * @return [type]       [description]
     */
    public function create($code = "")
    {
        $this->createBg();
        if(strlen($code)){
            $this->code = str_split($code);
        }
        else{
            $this->createCode();
        }
        $this->createLine();
        $this->createFont();
        $this->outPut();
    }

    /**
     * 获取验证码
     *
     * @return [type] [description]
     */
    public function getCode()
    {
        return implode('', $this->code);
    }

    /**
     * 设置验证码
     *
     * @param [type] $code [description]
     */
    public function setCode($code)
    {
    	$this->code = str_split($code);
    	return $this;
    }

    /**
     * 设置验证码长度
     *
     * @param integer $len [description]
     */
    public function setLen($len = 4)
    {
    	$this->codelen = $len;
    	return $this;
    }

}
