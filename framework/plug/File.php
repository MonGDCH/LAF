<?php
namespace Laf\plug;

use RuntimeException;

/**
 * 文章操作类
 *
 * @author Mon <985558837@qq.com>
 * @version  v1.0
 */
class File
{
    /**
     * 创建目录
     *
     * @param  [type] $dirPath 目录路径
     * @return [type]          [description]
     */
    public function createDir($dirPath)
    {
        return !is_dir($dirPath) && mkdir($dirPath, 0755, true);
    }

    /**
     * 创建文件
     *
     * @param  [type]  $content 写入内容
     * @param  [type]  $path    文件路径
     * @param  boolean $append  存在文件是否继续写入
     * @return [type]           [description]
     */
    public function createFile($content, $path, $append = true)
    {
        $dirPath = dirname($path);
        is_dir($dirPath) or $this->createDir($dirPath);
        // 添加写入
        if($append){
            return file_put_contents($path, $content, FILE_APPEND);
        }
        // 重新写入
        else{
            return file_put_contents($path, $content);
        }
    }

    /**
     * 分卷记录文件
     *
     * @param  [type]  $content 记录的内容
     * @param  [type]  $path    保存的路径, 不含后缀
     * @param  integer $maxSize 文件最大尺寸
     * @param  string  $rollNum 分卷数
     * @return [type]           [description]
     */
    public function subsectionFile($content, $path, $postfix = '.log', $maxSize = 20480000, $rollNum = 3)
    {
        $destination = $path . $postfix;
        $contentLength = strlen($content);
        // 判断写入内容的大小
        if($contentLength > $maxSize){
            throw new RuntimeException("Save content size cannot exceed {$maxSize}, content size: {$contentLength}");
        }
        // 判断记录文件是否已存在，存在时文件大小不足写入
        elseif(file_exists($destination) && floor($maxSize) < (filesize($destination) + $contentLength))
        {
            // 超出剩余写入大小，分卷写入
            $this->shiftFile($path, $postfix, $rollNum);
            return $this->createFile($content, $destination, false);
        }
        // 不存在文件或文件大小足够继续写入
        else{
            return $this->createFile($content, $destination);
        }
    }

    /**
     * 删除文件
     *
     * @param  [type] $path [description]
     * @return [type]       [description]
     */
    public function rm($path)
    {
        if(file_exists($path)){
            return unlink($path);
        }
        return true;
    }

    /**
     * 分卷重命名文件
     *
     * @param  [type] $path [description]
     * @return [type]       [description]
     */
    protected function shiftFile($path, $postfix, $rollNum)
    {
        // 判断是否存在最老的一份文件，存在则删除
        $oldest = $this->buildShiftName($path, ($rollNum - 1));
        $oldestFile = $oldest . $postfix;
        if(!$this->rm($oldestFile)){
            throw new RuntimeException("Failed to delete old file, oldFileName: {$oldestFile}");
        }

        // 循环重命名文件
        for($i = ($rollNum - 2); $i >= 0; $i--)
        {
            // 最新的一卷不需要加上分卷号
            if($i == 0){
                $oldFile = $path;
            }
            // 获取分卷号文件名称
            else{
                $oldFile = $this->buildShiftName($path, $i);
            }

            // 重命名文件
            $oldFileName = $oldFile . $postfix;
            if(file_exists($oldFileName)){
                $newFileNmae = $this->buildShiftName($path, ($i + 1)) . $postfix;
                // 重命名
                if( !rename($oldFileName, $newFileNmae) ){
                    throw new RuntimeException("Failed to rename volume file name, oldFileName: {$oldFileName}, newFileNmae: {$newFileNmae}");
                }
            }
        }
    }

    /**
     * 构造分卷文件名称
     *
     * @param  [type] $fileName 文件名称，不含后缀
     * @param  string $num      分卷数
     * @return [type]           [description]
     */
    protected function buildShiftName($fileName, $num)
    {
        return $fileName . '_' . $num;
    }
}