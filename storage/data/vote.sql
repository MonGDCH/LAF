
-- 投票活动系统相关SQL

CREATE TABLE IF NOT EXISTS `vote_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group` varchar(20) NOT NULL DEFAULT '' COMMENT '配置分组',
  `index` varchar(20) NOT NULL COMMENT '配置索引',
  `name` varchar(20) NOT NULL COMMENT '名称',
  `value` text NOT NULL COMMENT '配置值',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1:有效,2:无效',
  `update_time` int(10) unsigned NOT NULL,
  `create_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `item` (`group`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统配置表';

CREATE TABLE IF NOT EXISTS `vote_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `moble` varchar(30) NOT NULL COMMENT '手机号,帐号',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `manifesto` varchar(255) NOT NULL DEFAULT '' COMMENT '参赛宣言',
  `imgs` varchar(255) NOT NULL DEFAULT '' COMMENT '参赛照片',
  `number` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '获得票数',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1:有效,2:无效',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  `create_time` int(10) unsigned NOT NULL COMMENT '注册时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `moble` (`moble`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';
