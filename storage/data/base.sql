
CREATE TABLE IF NOT EXISTS `pz_user` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`email` varchar(250) NOT NULL COMMENT '用户邮箱',
	`moble` varchar(30) NOT NULL COMMENT '手机号',
	`username` varchar(50) NOT NULL COMMENT '用户名',
	`password` char(32) NOT NULL COMMENT '密码',
	`salt` varchar(30) NOT NULL COMMENT '加密盐',
	`status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1:有效,2:无效',
	`update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
	`create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
	PRIMARY KEY (`id`),
	UNIQUE KEY `user_name` (`username`),
	UNIQUE KEY `user_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';

CREATE TABLE IF NOT EXISTS `pz_account` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '所属用户ID',
	`ip` varchar(30) NOT NULL COMMENT '机器IP',
	`port` int(6) unsigned NOT NULL COMMENT '机器端口',
	`encrypt` varchar(50) NOT NULL DEFAULT 'aes-128-ctr' COMMENT '加密方式',
	`total_flow` decimal(10, 2) NOT NULL DEFAULT '0.00'COMMENT '总流量,单位:G',
	`use_flow` decimal(10, 2) NOT NULL DEFAULT '0.00' COMMENT '剩余流量,单位:G',
	`account` varchar(30) NOT NULL COMMENT '登录用户名',
	`password` varchar(32) NOT NULL COMMENT '密码',
	`status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1:有效,2:无效',
	`update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
	`create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
	PRIMARY KEY (`id`),
	UNIQUE KEY `item` (`ip`, `port`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='帐号表';

CREATE TABLE IF NOT EXISTS `pz_invite` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '注册用户ID',
	`pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '关联设置帐号',
	`code` varchar(30) NOT NULl COMMENT '推荐码',
	`isuse` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0:未使用; 1:已使用',
	`status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1:有效,0:无效',
	`update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
	`create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
	PRIMARY KEY (`id`),
	UNIQUE KEY `code_item` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='推荐码表';

CREATE TABLE IF NOT EXISTS `pz_admin` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`username` varchar(50) NOT NULL COMMENT '用户名',
	`password` char(32) NOT NULL COMMENT '密码',
	`salt` varchar(30) NOT NULL COMMENT '加密盐',
	`status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1:有效,2:无效',
	`update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
	`create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员表';


CREATE TABLE IF NOT EXISTS `pz_oper` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`uid` int(11) unsigned NOT NULL COMMENT '用户ID',
	`pid` int(11) unsigned NOT NULL COMMENT '帐号ID',
	`ip` varchar(30) NOT NULL COMMENT '机器IP',
	`port` int(6) unsigned NOT NULL COMMENT '机器端口',
	`cate` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '操作类型,1:修改密码',
	`value` varchar(250) NOT NULL DEFAULT '' COMMENT '操作信息',
	`status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1:有效,2:无效',
	`update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
	`create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户异步操作表';