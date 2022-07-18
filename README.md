# LAF

PHP高性能快速开发框架


## 安装

```
composer create-project mongdch/laf
```

## Version

#### 3.2.2

- 更新依赖
- 优化代码


#### 3.2.1

- 修正route、config缓存逻辑


#### 3.2.0

- 更新依赖
- 优化结构代码
- 增加代码注解
- 增加`optimize`指令，用于优化应用


#### 3.1.0

- 优化代码，移除多余的前端文件


#### 3.0.2

- 优化业务，整理代码


#### 3.0.1

- 优化整理代码


#### 3.0.0

- 优化代码，返璞归真
- 增强对workerman的支持


#### 2.2.2

- 优化代码，更新依赖
- 增加代码注解


#### 2.2.1

- 优化代码结构，优化依赖


#### 2.2.0

- 发布2.2.0LTS版本
- 优化代码及业务结构
- 增加gatewayworker支持，通过运行根目录下server.php或service.bat启动，可在service.php配置文件中修改对应的配置


#### 2.1.0

- 发布2.1.0LTS版本
- 调整服务类库


#### 2.0.1

- 正式发布2.0.1版本


#### 2.0.0

- 解耦代码
- 更新依赖
- 优化代码


#### 1.2.1

- 修复已知BUG


#### 1.2.0

- 调整项目依赖
- 优化微信类库 


#### 1.1.0

- 调整PHP版本依赖，采用php7版本
- 调整代码结构，增加console命令行功能
- 调整Controller基类，分成ApiController和ViewController，细化功能实现
- 增加路由缓存功能，用于优化系统性能，通过console指令执行: php laf router cache 调用


#### 1.0.0

- 调整应用执行方式，取消从index.php中定义，改为从bootstrap.php，并分化为3中模式
- 调整Controller基类，增加ApiController和ViewController，细化功能实现
- 修复日志服务在部分环境下日志文件命名问题的BUG
- 调整framework部分组件结构

