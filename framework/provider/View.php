<?php

namespace Laf\provider;

use mon\factory\Container;
use mon\template\View as Template;

/**
 * 视图服务
 */
class View extends Template
{
    /**
     * 构造方法
     */
    public function __construct()
    {
        parent::__construct();

        $config = Container::instance()->config->get('view');
        $this->path = $config['path'];
        $this->ext = $config['ext'];
    }
}
