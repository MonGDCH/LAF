
-- 计数器系统相关SQL

CREATE TABLE IF NOT EXISTS `cnt_mark` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`key1` varchar(100) NOT NULL COMMENT '索引key1',
	`key2` varchar(100) NOT NULL COMMENT '索引key2',
	`ponds` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '采用的计数池数量',
	`remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
	`uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '预留字段，整形',
	`str` varchar(255) NOT NULL DEFAULT '' COMMENT '预留字段，字符串',
	`status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1:有效,2:无效',
	`create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
	PRIMARY KEY (`id`),
	UNIQUE KEY `mark` (`key1`, `key2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='计数器类型表';


CREATE TABLE IF NOT EXISTS `cnt_record` (
	`mark_id` int(11) unsigned NOT NULL COMMENT '类型ID',
	`pond` tinyint(3) unsigned NOT NUll COMMENT '计数器池',
	`count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '计数',
	`upadte_time` int(10) unsigned NOT NULL COMMENT '更新时间',
	`create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
	PRIMARY KEY (`mark_id`,`pond`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='计数器计数表';