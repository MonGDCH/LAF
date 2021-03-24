<?php

namespace Laf\command;

use mon\console\Command;
use mon\console\Input;
use mon\console\Output;
use mon\util\Container;

/**
 * 生成指令
 *
 * @author Mon <98555883@qq.com>
 * @version v1.1 优化代码，增加生成文件时间
 */
class Make extends Command
{
    /**
     * App目录
     *
     * @var string
     */
    protected $app_path = ROOT_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR;

    /**
     * 文章后缀
     *
     * @var string
     */
    protected $ext = '.php';

    /**
     * 当前日期
     *
     * @var string
     */
    protected $now;

    /**
     * 错误信息
     *
     * @var string
     */
    protected $error;

    /**
     * 执行指令
     *
     * @param Input $in 输入实例
     * @param Output $out 输出实例
     * @return integer
     */
    public function execute($in, $out)
    {
        $args = $this->getArgv($in);
        $config = $this->parseArgv($args);
        if (!$config) {
            $out->write('format:');
            $help = [
                'Model: php LAF make model name table',
                'Controller: php LAF make controller name',
                'Validate: php LAF make validate name',
                'Befor: php LAF make befor name',
                'After: php LAF make after name',
                'Command: php LAF make command name',
            ];
            $out->dataList($help);
            return $out->block($this->error, 'ERROR');
        }
        // 获取当前日期
        $this->now = date('Y-m-d', time());
        // 解析类型，执行make操作
        switch ($config['type']) {
            case 'model':
                if (isset($args['table']) && !empty($args['table'])) {
                    $table = $args['table'];
                } elseif (isset($args[2]) && !empty($args[2])) {
                    $table = $args[2];
                } else {
                    $table = $config['name'];
                }
                $make = $this->model($config['name'], $table);
                break;
            case 'controller':
                $make = $this->controller($config['name']);
                break;
            case 'validate':
                $make = $this->validate($config['name']);
                break;
            case 'befor':
                $make = $this->befor($config['name']);
                break;
            case 'after':
                $make = $this->after($config['name']);
                break;
            case 'command':
                $make = $this->command($config['name']);
                break;
            default:
                return $out->block('Type Error!', 'ERROR');
                break;
        }

        if (!$make) {
            return $out->block($this->error, 'ERROR');
        }

        return $out->write('Make ' . $config['type'] . ' ' . ucfirst($config['name']) . ' Success!');
    }

    /**
     * 创建模型
     *
     * @param string $name  模型名称
     * @param string $table 表名
     * @return boolean
     */
    protected function model($name, $table)
    {
        $path = $this->app_path . 'model';
        $model = ucfirst($name);
        $model_file = $path . DIRECTORY_SEPARATOR . $model . $this->ext;
        if (file_exists($model_file)) {
            $this->error = 'Model[' . $model . '] Exists!';
            return false;
        }
        $template = $this->load('model');
        if (!$template) {
            return false;
        }
        $content = sprintf($template, $model, $model, $this->now, $model, $table);
        $save = Container::instance()->file->createFile($content, $model_file, false);
        if (!$save) {
            $this->error = 'Save Model Error![' . $model_file . ']';
            return false;
        }

        return true;
    }

    /**
     * 创建控制器
     *
     * @param  string $name 控制名称
     * @return boolean
     */
    protected function controller($name)
    {
        $path = $this->app_path . 'http' . DIRECTORY_SEPARATOR . 'controller';
        $ctrl = ucfirst($name);
        $ctrl_file = $path . DIRECTORY_SEPARATOR . $ctrl . $this->ext;
        if (file_exists($ctrl_file)) {
            $this->error = 'Controller[' . $ctrl . '] Exists!';
            return false;
        }
        // 加载模版
        $template = $this->load('controller');
        if (!$template) {
            return false;
        }
        // Make
        $content = sprintf($template, $ctrl, $ctrl, $this->now, $ctrl);
        $save = Container::instance()->file->createFile($content, $ctrl_file, false);
        if (!$save) {
            $this->error = 'Save Controller Error![' . $ctrl_file . ']';
            return false;
        }

        return true;
    }

    /**
     * 创建验证器
     *
     * @param  string $name 验证器名称
     * @return boolean
     */
    protected function validate($name)
    {
        $path = $this->app_path . 'validate';
        $ctrl = ucfirst($name);
        $ctrl_file = $path . DIRECTORY_SEPARATOR . $ctrl . $this->ext;
        if (file_exists($ctrl_file)) {
            $this->error = 'Validate[' . $ctrl . '] Exists!';
            return false;
        }
        // 加载模版
        $template = $this->load('validate');
        if (!$template) {
            return false;
        }
        // Make
        $content = sprintf($template, $ctrl, $ctrl, $this->now, $ctrl);
        $save = Container::instance()->file->createFile($content, $ctrl_file, false);
        if (!$save) {
            $this->error = 'Save Validate Error![' . $ctrl_file . ']';
            return false;
        }

        return true;
    }

    /**
     * 创建前置件
     *
     * @param  string $name 前置件名称
     * @return boolean
     */
    protected function befor($name)
    {
        $path = $this->app_path . 'http' . DIRECTORY_SEPARATOR . 'middleware';
        $ctrl = ucfirst($name);
        $ctrl_file = $path . DIRECTORY_SEPARATOR . $ctrl . $this->ext;
        if (file_exists($ctrl_file)) {
            $this->error = 'Befor[' . $ctrl . '] Exists!';
            return false;
        }
        // 加载模版
        $template = $this->load('befor');
        if (!$template) {
            return false;
        }
        // Make
        $content = sprintf($template, $ctrl, $ctrl, $this->now, $ctrl);
        $save = Container::instance()->file->createFile($content, $ctrl_file, false);
        if (!$save) {
            $this->error = 'Save Befor Error![' . $ctrl_file . ']';
            return false;
        }

        return true;
    }

    /**
     * 创建后置件
     *
     * @param  string $name 控制名称
     * @return boolean
     */
    protected function after($name)
    {
        $path = $this->app_path . 'http' . DIRECTORY_SEPARATOR . 'middleware';
        $ctrl = ucfirst($name);
        $ctrl_file = $path . DIRECTORY_SEPARATOR . $ctrl . $this->ext;
        if (file_exists($ctrl_file)) {
            $this->error = 'After[' . $ctrl . '] Exists!';
            return false;
        }
        // 加载模版
        $template = $this->load('after');
        if (!$template) {
            return false;
        }
        // Make
        $content = sprintf($template, $ctrl, $ctrl, $this->now, $ctrl);
        $save = Container::instance()->file->createFile($content, $ctrl_file, false);
        if (!$save) {
            $this->error = 'Save After Error![' . $ctrl_file . ']';
            return false;
        }

        return true;
    }

    /**
     * 创建指令
     *
     * @param  string $name 控制名称
     * @return boolean
     */
    protected function command($name)
    {
        $path = $this->app_path . 'console' . DIRECTORY_SEPARATOR . 'command';
        $ctrl = ucfirst($name);
        $ctrl_file = $path . DIRECTORY_SEPARATOR . $ctrl . $this->ext;
        if (file_exists($ctrl_file)) {
            $this->error = 'Command[' . $ctrl . '] Exists!';
            return false;
        }
        // 加载模版
        $template = $this->load('command');
        if (!$template) {
            return false;
        }
        // Make
        $content = sprintf($template, $ctrl, $ctrl, $this->now, $ctrl);
        $save = Container::instance()->file->createFile($content, $ctrl_file, false);
        if (!$save) {
            $this->error = 'Save Command Error![' . $ctrl_file . ']';
            return false;
        }

        return true;
    }

    /**
     * 加载模版
     *
     * @param  string $name 模板名称
     * @return mixed
     */
    protected function load($name)
    {
        $path = __DIR__ . DIRECTORY_SEPARATOR . 'make' . DIRECTORY_SEPARATOR . $name . '.tpl';
        $content = file_get_contents($path);
        if (!$content) {
            $this->error = 'Get Template Error![' . $path . ']';
            return false;
        }

        return $content;
    }

    /**
     * 解析参数
     *
     * @param  array $args 参数值
     * @return mixed
     */
    protected function parseArgv(array $args)
    {
        if (empty($args)) {
            $this->error = 'Invalid Argv!';
            return false;
        }

        $data = [
            'type' => null,
            'name' => null,
        ];

        foreach ($args as $k => $v) {
            switch ($k) {
                case '0':
                    $data['type'] = $v;
                    break;
                case '1':
                    $data['name'] = $v;
                    break;
                case 'type':
                    $data['type'] = $v;
                    break;
                case 'name':
                    $data['name'] = $v;
                    break;
            }
        }

        if (empty($data['type']) || empty($data['name'])) {
            $this->error = 'Params type or name not found!';
            return false;
        }

        return $data;
    }

    /**
     * 获取入参
     *
     * @param  Input  $in 入参
     * @return array
     */
    protected function getArgv($in)
    {
        $args = $in->getArgs();
        $lopt = $in->getlopt();

        // 合并，指定参数优先
        foreach ($lopt as $k => $v) {
            $args[$k] = $v;
        }

        return $args;
    }
}
