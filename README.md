# LAF

基于PHP5.6+的高性能快速开发框架


## 安装

```
composer create-project mongdch/laf
```

## Versuib

> v1.0.1

- 调整应用执行方式，取消从index.php中定义，改为从bootstrap.php，并分化为3中模式
- 调整Controller基类，增加ApiController和ViewController，细化功能实现
- 修复日志服务在部分环境下日志文件命名问题的BUG
- 调整framework部分组件结构