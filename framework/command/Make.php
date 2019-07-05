<?php

namespace Laf\command;

use FApi\Route;
use Mon\console\Command;
use Mon\console\Input;
use Mon\console\Output;

/**
 * 生成指令
 *
 * @author Mon <98555883@qq.com>
 * @version v1.0
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
     * 错误信息
     *
     * @var [type]
     */
    protected $error;

    /**
     * 执行指令
     *
     * @return [type] [description]
     */
    public function execute(Input $in, Output $out)
    {
        $out->write('format:');
        $help = [
            'Model: php LAF make model name table',
            'Controller: php LAF make controller name',
            'Validate: php LAF make validate name',
            'Befor: php LAF make befor name',
            'After: php LAF make after name',
        ];
        $out->list($help);

        $argv = $this->getArgv($in);
        $config = $this->parseArgv($argv);
        if (!$config) {
            return $out->block($this->error, 'ERROR');
        }
        // 解析类型，执行make操作
        switch ($config['type']) {
            case 'model':
                if (isset($argv['table']) && !empty($argv['table'])) {
                    $table = $argv['table'];
                } elseif (isset($argv[2]) && !empty($argv[2])) {
                    $table = $argv[2];
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
            default:
                return $out->block('Type Error!', 'ERROR');
                break;
        }

        if (!$make) {
            return $out->block($this->error, 'ERROR');
        }

        return $out->write('Make ' . $config['type'] . ' Success!');
    }

    /**
     * 创建模型
     *
     * @param string $name  模型名称
     * @param string $table 表名
     * @return boolean
     */
    protected function model(string $name, string $table): bool
    {
        $path = $this->app_path . 'Model';
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
        $content = sprintf($template, $model, $model, $model, $table);
        $save = file_put_contents($model_file, $content);
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
     * @return [type]       [description]
     */
    protected function controller(string $name): bool
    {
        $path = $this->app_path . 'Http' . DIRECTORY_SEPARATOR . 'Controller';
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
        $content = sprintf($template, $ctrl, $ctrl, $ctrl);
        $save = file_put_contents($ctrl_file, $content);
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
     * @return [type]       [description]
     */
    protected function validate(string $name): bool
    {
        $path = $this->app_path . 'Validate';
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
        $content = sprintf($template, $ctrl, $ctrl, $ctrl);
        $save = file_put_contents($ctrl_file, $content);
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
     * @return [type]       [description]
     */
    protected function befor(string $name): bool
    {
        $path = $this->app_path . 'Http' . DIRECTORY_SEPARATOR . 'Befor';
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
        $content = sprintf($template, $ctrl, $ctrl, $ctrl);
        $save = file_put_contents($ctrl_file, $content);
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
     * @return [type]       [description]
     */
    protected function after(string $name): bool
    {
        $path = $this->app_path . 'Http' . DIRECTORY_SEPARATOR . 'After';
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
        $content = sprintf($template, $ctrl, $ctrl, $ctrl);
        $save = file_put_contents($ctrl_file, $content);
        if (!$save) {
            $this->error = 'Save After Error![' . $ctrl_file . ']';
            return false;
        }

        return true;
    }


    /**
     * 加载模版
     *
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    protected function load(string $name)
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
     * @param  [type] $argv [description]
     * @return [type]       [description]
     */
    protected function parseArgv(array $argv)
    {
        if (empty($argv)) {
            $this->error = 'Invalid Argv!';
            return false;
        }

        $data = [
            'type' => null,
            'name' => null,
        ];

        foreach ($argv as $k => $v) {
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
     * @param  Input  $in [description]
     * @return [type]     [description]
     */
    protected function getArgv(Input $in)
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
