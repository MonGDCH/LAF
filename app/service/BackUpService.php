<?php

namespace app\service;

use mon\orm\Db;
use mon\util\Sql;
use mon\env\Config;
use mon\util\Instance;

/**
 * Mysql数据表备份迁移服务
 * 
 * @author Mon <985558837@qq.com>
 * @version 1.0.0
 */
class BackUpService
{
    use Instance;

    /**
     * 文件指针
     *
     * @var resource
     */
    private $fp = null;

    /**
     * 数据库配置
     *
     * @var array
     */
    private $dbconfig = [];

    /**
     * 保存路径
     *
     * @var string
     */
    private $path = RUNTIME_PATH . '/data/';

    /**
     * 错误信息
     *
     * @var mixed
     */
    private $error;

    /**
     * 获取错误信息
     *
     * @return mixed
     */
    public function getError()
    {
        $error = $this->error;
        $this->error = '';
        return $error;
    }

    /**
     * 析构方法，用于关闭文件资源
     */
    public function __destruct()
    {
        if (!is_null($this->fp)) {
            @fclose($this->fp);
        }
    }

    /**
     * 获取MySQL链接
     *
     * @return \mon\orm\db\Query
     */
    public function connect()
    {
        $config = $this->dbconfig ? $this->dbconfig :  Config::instance()->get('database');
        return Db::connect($config);
    }

    /**
     * 设置保存路径
     *
     * @param string $path 保存路径
     * @return BackUpService
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * 设置数据库连接必备参数
     *
     * @param array $dbconfig 数据库连接配置信息
     * @return BackUpService
     */
    public function setDbConfig(array $dbconfig)
    {
        $this->dbconfig = $dbconfig;
        return $this;
    }

    /**
     * 备份表
     *
     * @param string|array|null $table
     * @return boolean
     */
    public function backup($table)
    {
        switch (gettype($table)) {
            case 'string':
                $save = $this->backupTable($table);
                if (!$save) {
                    $this->error = '备份表失败[' . $table . ']';
                    return false;
                }
                if (!is_null($this->fp)) {
                    @fclose($this->fp);
                    $this->fp = null;
                }
                break;
            case 'array':
                foreach ($table as $item) {
                    $save = $this->backupTable($item);
                    if (!$save) {
                        $this->error = '备份表失败[' . $item . ']';
                        return false;
                    }
                    if (!is_null($this->fp)) {
                        @fclose($this->fp);
                        $this->fp = null;
                    }
                }
                break;
            case 'NULL':
                $data = $this->getAllTable();
                $tables = [];
                foreach ($data as $item) {
                    $tables[] = $item['Name'];
                }
                return $this->backup($tables);
            default:
                $this->error = '未支持的table参数类型';
                return false;
        }

        return true;
    }

    /**
     * 导入数据库文件
     *
     * @param string $file 数据库文件路径
     * @return boolean
     */
    public function import($file)
    {
        // 读取sql文件
        $sqls = Sql::instance()->parseFile($file);
        // 一次性写入sql
        foreach ($sqls as $sql) {
            $this->connect()->execute($sql);
        }

        return true;
    }

    /**
     * 获取所有表信息
     *
     * @return array
     */
    public function getAllTable()
    {
        $data = $this->connect()->query("SHOW TABLE STATUS");
        return $data;
    }

    /**
     * 备份表结构
     *
     * @param string $table 表名
     * @param integer $start 起始位
     * @return boolean
     */
    protected function backupTable($table, $start = 0)
    {
        // 备份表结构
        if ($start == 0) {
            $result = $this->connect()->query("SHOW CREATE TABLE `{$table}`");
            $sql = "\n";
            $sql .= "-- -----------------------------\n";
            $sql .= "-- Mon MySQL Data Transfer \n";
            $sql .= "-- \n";
            $sql .= "-- Date : " . date("Y-m-d H:i:s") . "\n";
            $sql .= "-- Table for `{$table}`\n";
            $sql .= "-- -----------------------------\n\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= trim($result[0]['Create Table']) . ";\n\n";
            if (false === $this->write($sql, $table)) {
                return false;
            }
        }
        // 数据总数
        $count = $this->connect()->table($table)->count();
        // 备份表数据
        if ($count) {
            $first = false;
            // 写入数据注释
            if ($start == 0) {
                // 获取表字  
                $fieldInfo = $this->getFields($table);
                $field = array_keys($fieldInfo);
                $fields = implode('`, `', $field);

                $sql = "-- -----------------------------\n";
                $sql .= "-- Records of `{$table}`\n";
                $sql .= "-- -----------------------------\n\n";
                $sql .= "INSERT INTO `{$table}`(`{$fields}`) VALUES \r\n";

                $first = true;
                $this->write($sql, $table);
            }
            // 备份数据记录
            $result = $this->connect()->query("SELECT * FROM `{$table}` LIMIT {$start}, 500");
            foreach ($result as $row) {
                $row = array_map('addslashes', $row);
                $sql = "('" . str_replace(["\r", "\n"], ['\\r', '\\n'], implode("', '", $row)) . "')";
                if ($first) {
                    $first = false;
                } else {
                    $sql = ",\n" . $sql;
                }
                if (false === $this->write($sql, $table)) {
                    return false;
                }
            }
            unset($result);
            // 还有更多数据
            if ($count > $start + 500) {
                return $this->backupTable($table, $start + 500);
            }
        }
        // 备份下一表
        return true;
    }

    /**
     * 写入SQL语句
     *
     * @param string $sql 要写入的SQL语句
     * @return boolean     true - 写入成功，false - 写入失败！
     */
    protected function write($sql, $table)
    {
        $this->open($table);
        return @fwrite($this->fp, $sql);
    }

    /**
     * 打开一个文件，用于写入数据
     *
     * @param integer $size 写入数据的大小
     * @return void
     */
    protected function open($table)
    {
        if (!$this->fp) {
            $this->checkPath($this->path);
            $filename = "{$this->path}{$table}.sql";
            $this->fp = @fopen($filename, 'a');
        }
    }

    /**
     * 检查目录是否存在
     *
     * @param string $path 文件目录路径
     * @return boolean
     */
    protected function checkPath($path)
    {
        if (is_dir($path)) {
            return true;
        }
        if (mkdir($path, 0755, true)) {
            return true;
        }
        return false;
    }

    /**
     * 获取表字段信息
     *
     * @param  string $table 表名
     * @return array 表字段信息
     */
    protected function getFields($table)
    {
        $sql = 'SHOW COLUMNS FROM ' . $table;
        $result = $this->connect()->query($sql);
        $info = [];
        if ($result) {
            foreach ($result as $key => $val) {
                $val = array_change_key_case($val);
                $info[$val['field']] = [
                    'name'    => $val['field'],
                    'type'    => $val['type'],
                    'notnull' => (bool) ('' === $val['null']),
                    'default' => $val['default'],
                    'primary' => (strtolower($val['key']) == 'pri'),
                    'autoinc' => (strtolower($val['extra']) == 'auto_increment'),
                ];
            }
        }

        return $info;
    }
}
