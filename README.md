# LAF

PHP高性能快速开发框架


## 安装

```
composer create-project mongdch/laf
```

## Version

> v2.2.1

- 优化代码结构，优化依赖

> v2.2.0

- 发布2.2.0LTS版本
- 优化代码及业务结构
- 增加gatewayworker支持

> v2.1.0

- 发布2.1.0LTS版本
- 调整服务类库

> v2.0.1

- 正式发布2.0.1版本

> v2.0.0

- 解耦代码
- 更新依赖
- 优化代码

> v1.2.1

- 修复已知BUG

> v1.2.0

- 调整项目依赖
- 优化微信类库 

> v1.1.0

- 调整PHP版本依赖，采用php7版本
- 调整代码结构，增加console命令行功能
- 调整Controller基类，分成ApiController和ViewController，细化功能实现
- 增加路由缓存功能，用于优化系统性能，通过console指令执行: php laf router cache 调用

> v1.0.0

- 调整应用执行方式，取消从index.php中定义，改为从bootstrap.php，并分化为3中模式
- 调整Controller基类，增加ApiController和ViewController，细化功能实现
- 修复日志服务在部分环境下日志文件命名问题的BUG
- 调整framework部分组件结构


#### console说明

1. 在bootstrap/config/commands.php中添加自定义指令
2. 执行指令

```base
# php laf [指令名称] [...指令参数]

php laf router help

```

更多说明请在代码备注中查看
