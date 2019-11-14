<?php
/**
 * Created by PhpStorm.
 * User: gly
 * Date: 2019/10/28
 * Time: 19:18
 */


CREATE TABLE `attachment` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `attachment` varchar(255) NOT NULL,
  `type` tinyint(3) UNSIGNED NOT NULL,
  `createtime` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1;


CREATE TABLE `bill` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) UNSIGNED NOT NULL COMMENT '用户id',
  `currency_id` int(11) UNSIGNED NOT NULL COMMENT '币种',
  `money` decimal(10,3) NOT NULL COMMENT '红包金额',
  `order_no` varchar(50) NOT NULL COMMENT '订单号',
  `remark` varchar(200) NOT NULL COMMENT '备注信息',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1;


CREATE TABLE `currency` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL COMMENT '排序',
  `name` varchar(20) NOT NULL COMMENT '币种名称',
  `icon` varchar(255) DEFAULT NULL COMMENT '图标地址',
  `tag` varchar(20) NOT NULL COMMENT '币种标识',
  `create_time` int(11) NOT NULL COMMENT '注册时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `cash_service_ratio` decimal(5,2) NOT NULL COMMENT '提现手续费比例',
  `cash_service_max` decimal(8,2) NOT NULL COMMENT '最高提现手续费 0则不限制',
  `cash_min` decimal(10,2) NOT NULL COMMENT '最小提现金额',
  `cash_max` decimal(10,2) NOT NULL COMMENT '最高提现金额',
  `cash_review` decimal(10,2) NOT NULL COMMENT '提现审核额度',
  `state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态1:正常2:禁用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1;

CREATE TABLE `dis_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `currency_id` int(11) UNSIGNED NOT NULL COMMENT '币种',
  `tree_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '财富树id',
  `redgroup_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '红包群',
  `redpacket_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '红包id',
  `uid` int(11) UNSIGNED NOT NULL COMMENT '奖励/消费 用户 1为总部',
  `from_uid` int(11) UNSIGNED NOT NULL COMMENT '来源用户id 0 为本人',
  `order_no` varchar(50) NOT NULL COMMENT '订单号',
  `level` tinyint(4) NOT NULL COMMENT '123',
  `scene` tinyint(1) NOT NULL COMMENT '1.财富树 2.红包',
  `money` decimal(10,3) NOT NULL COMMENT '金额',
  `remark` varchar(200) NOT NULL COMMENT '备注',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1;

--
-- 表的结构 `opinion`
--

CREATE TABLE `opinion` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) UNSIGNED NOT NULL COMMENT '用户id',
  `email` varchar(50) NOT NULL COMMENT '邮箱',
  `content` varchar(400) NOT NULL COMMENT '内容',
  `create_time` int(11) NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='意见反馈' ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1;

--
-- 表的结构 `pay_order`
--

CREATE TABLE `pay_order` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `currency_id` int(11) UNSIGNED DEFAULT NULL COMMENT '币种',
  `order_no` varchar(50) NOT NULL COMMENT '订单号',
  `uid` int(11) UNSIGNED NOT NULL COMMENT '用户',
  `money` decimal(10,2) NOT NULL COMMENT '用户',
  `state` tinyint(1) NOT NULL COMMENT '支付状态 1:待支付 2:已支付',
  `type` tinyint(1) NOT NULL COMMENT '用途 1:创建财富树 2.加入财富树 3:升级财富树 4:创建红包 5: 支付红包',
  `pay_time` int(11) NOT NULL COMMENT '支付时间',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1;

--
-- 表的结构 `tree`
--

CREATE TABLE `tree` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL COMMENT '财富树名称',
  `order_no` varchar(50) DEFAULT NULL COMMENT '订单号',
  `room_number` varchar(50) DEFAULT NULL COMMENT '房间号码',
  `uid` int(11) UNSIGNED NOT NULL COMMENT '用户id',
  `currency_id` int(11) UNSIGNED NOT NULL COMMENT '币种',
  `level` tinyint(4) NOT NULL COMMENT '层级',
  `limit` tinyint(4) NOT NULL COMMENT '可推人数',
  `money` decimal(8,2) NOT NULL COMMENT '加入价格',
  `growth_ratio` decimal(8,2) NOT NULL COMMENT '增长比率',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- 表的结构 `tree_log`
--

CREATE TABLE `tree_log` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `currency_id` int(11) UNSIGNED DEFAULT NULL COMMENT '币种',
  `tree_id` int(11) UNSIGNED DEFAULT NULL COMMENT '财富树id',
  `uid` int(11) UNSIGNED DEFAULT NULL COMMENT '奖励/消费 用户 -1为总部',
  `from_uid` int(11) UNSIGNED DEFAULT NULL COMMENT '来源用户id 0 为本人',
  `loss_uid` int(11) UNSIGNED DEFAULT NULL COMMENT '丢失人id 0为无',
  `order_no` varchar(50) DEFAULT NULL COMMENT '订单号',
  `scene` tinyint(4) DEFAULT NULL COMMENT '0.创建 1.加入 2.升级',
  `money` decimal(10,3) DEFAULT NULL COMMENT '金额',
  `remark` varchar(200) DEFAULT NULL COMMENT '备注',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- 表的结构 `tree_upgrade`
--

CREATE TABLE `tree_upgrade` (
 `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tree_id` int(11) UNSIGNED NOT NULL COMMENT '财富树',
  `uid` int(11) UNSIGNED NOT NULL COMMENT '用户id',
  `level` tinyint(4) NOT NULL COMMENT '层级',
  `tolevel` tinyint(4) NOT NULL COMMENT '会员等级',
  `order_no` varchar(50) DEFAULT NULL COMMENT '订单号',
  `money` decimal(10,2) NOT NULL COMMENT '会员等级',
  `create_time` int(11) NOT NULL COMMENT '升级时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- 表的结构 `tree_user`
--

CREATE TABLE `tree_user` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tree_id` int(11) UNSIGNED NOT NULL COMMENT '财富树',
  `uid` int(11) UNSIGNED NOT NULL COMMENT '用户id',
  `puid` int(11) UNSIGNED DEFAULT NULL COMMENT '上级id',
  `level` tinyint(4) NOT NULL COMMENT '层级 0:代表群主 其他标识用于循环后做比较',
  `vip_level` tinyint(4) NOT NULL COMMENT '会员等级',
  `create_time` int(11) NOT NULL COMMENT '加入时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `order_no` varchar(50) DEFAULT NULL COMMENT '订单号 如创建用户则无',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE `user` (
  `uid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL COMMENT '账号',
  `password` varchar(50) NOT NULL COMMENT '密码',
  `nickname` varchar(20) DEFAULT NULL COMMENT '昵称',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像',
  `wechat_code` varchar(255) DEFAULT NULL COMMENT '微信二维码',
  `puid` int(11) NOT NULL COMMENT '上级id',
  `invite_code` varchar(20) DEFAULT NULL COMMENT '邀请码',
  `token` varchar(255) DEFAULT NULL COMMENT '登录token',
  `state` tinyint(1) DEFAULT 0 COMMENT '0:正常 其他禁用',
  `create_time` int(11) NOT NULL COMMENT '注册时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=2;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`uid`, `username`, `password`, `nickname`, `avatar`, `wechat_code`, `puid`, `invite_code`, `token`, `create_time`, `update_time`) VALUES
(1, 'root', 'krhi3rIVZ0U4Q+p3kcBv5Q==', '平台', NULL, NULL, 0, '11111', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOjEsImlhdCI6MTU2ODA5ODQ1NSwiZXhwIjoxNTY4MTA1NjU1LCJuYmYiOjE1NjgwOTg0NTUsInN1YiI6IiIsImp0aSI6IjQyMDQ3YTU2ZjliNTdmMWRjNmMxNTkwM2UxZWE3NDVkIn0.AmySthh7nqt7sOWHEo6HnJPS2qLt_v-bCnx8JUTR6TE', 1566356246, 1568098455);

-- --------------------------------------------------------


--
-- 表的结构 `wallet`
--

CREATE TABLE `wallet` (
 `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) UNSIGNED DEFAULT NULL COMMENT '用户id',
  `currency_id` int(11) UNSIGNED NOT NULL COMMENT '币种id',
  `total` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT '总金额',
  `free` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT '可用',
  `lock` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT '锁定',
  `consume` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT '消费',
  `address` varchar(255) DEFAULT NULL COMMENT '用户币种地址',
  `create_time` int(11) NOT NULL COMMENT '注册时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=2;

--
-- 转存表中的数据 `wallet`
--

INSERT INTO `wallet` (`id`, `uid`, `currency_id`, `total`, `free`, `lock`, `consume`, `address`, `create_time`, `update_time`) VALUES
(1, 1, 1, '0.000', '0.000', '0.000', '0.000', '', 1566382277, 1571144123);

CREATE TABLE `withdraw` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) NOT NULL COMMENT '订单号',
  `uid` int(11) UNSIGNED NOT NULL COMMENT '用户',
  `address` varchar(255) NOT NULL COMMENT '提现地址',
  `currency_id` int(11) UNSIGNED NOT NULL COMMENT '币种id',
  `total` decimal(10,3) NOT NULL COMMENT '总价格',
  `money` decimal(10,3) NOT NULL COMMENT '提现价格',
  `service` decimal(10,3) NOT NULL COMMENT '服务费',
  `state` tinyint(1) NOT NULL COMMENT '状态 1待审核 2:待入账 3:失败 4:已完成',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1;

CREATE TABLE `config` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '变量名',
  `group` varchar(30) NOT NULL DEFAULT '' COMMENT '分组',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '变量标题',
  `type` varchar(30) NOT NULL DEFAULT '' COMMENT '类型:string,text,int,bool,array,datetime,date,file',
  `value` text NOT NULL COMMENT '变量值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统配置' ROW_FORMAT=COMPACT AUTO_INCREMENT=1;

--
-- 表的结构 `profit`
--

CREATE TABLE `profit` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `currency_id` int(11) UNSIGNED NOT NULL COMMENT '币种',
  `rel_id` int(11) UNSIGNED NOT NULL COMMENT '关联id',
  `money` decimal(10,3) NOT NULL COMMENT '收益数量',
  `scene` tinyint(1) UNSIGNED NOT NULL COMMENT '场景1:创建财富树收益2:财富树下级收益3:推广收益',
  `create_time` int(11) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='平台收益' AUTO_INCREMENT=1;


CREATE TABLE `recharge` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) NOT NULL COMMENT '订单号',
  `uid` int(10) UNSIGNED NOT NULL COMMENT '充值用户',
  `address` varchar(255) NOT NULL COMMENT '充值地址',
  `count` decimal(10,3) NOT NULL COMMENT '充值数量',
  `coinType` varchar(50) NOT NULL COMMENT '币种',
  `create_time` int(11) NOT NULL COMMENT '充值时间',
  `status` int(11) NOT NULL COMMENT '1:未到账2:已到账',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='充值记录' ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1;




CREATE TABLE `admin_user` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `username` varchar(100) NOT NULL COMMENT '管理后台账号',
  `password` varchar(100) NOT NULL COMMENT '管理后台密码',
  `remark` varchar(100) DEFAULT NULL COMMENT '用户备注',
  `create_time` int(11) NOT NULL,
  `token` varchar(255) DEFAULT NULL COMMENT '登录token',
  `realname` varchar(100) DEFAULT NULL COMMENT '真实姓名',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态,1启用0禁用',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;



DROP TABLE IF EXISTS `notice`;
CREATE TABLE `notice` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned NOT NULL COMMENT '序号',
  `title` varchar(20) NOT NULL COMMENT '标题',
  `content` text NOT NULL COMMENT '内容',
  `type` tinyint(1) unsigned NOT NULL COMMENT '类型1:公告 2:规则',
  `create_time` int(11) NOT NULL COMMENT '添加时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='公告规则';

DROP TABLE IF EXISTS `transfer`;
CREATE TABLE `transfer` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fuid` int(11) unsigned NOT NULL COMMENT '转账人',
  `tuid` int(11) unsigned NOT NULL COMMENT '收款人',
  `currency_id` int(11) unsigned NOT NULL COMMENT '币种',
  `money` decimal(10, 3) NOT NULL COMMENT '金额',
  `remark` varchar(100) DEFAULT NULL COMMENT '备注',
  `create_time` int(11) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='转账记录';

--
-- 转存表中的数据 `admin_user`
--

INSERT INTO `admin_user` (`id`, `username`, `password`, `remark`, `create_time`, `token`, `realname`, `status`, `avatar`, `update_time`) VALUES
(1, 'admin', '06f08d6d3aec1986c828d780feff068e', '超级管理', 1567828924, 'f5ae01e3379a74532d9ce29219229dfa', 'Superman', 1, NULL, 1568293475),
(2, 'gly', '06f08d6d3aec1986c828d780feff068e', '专用', 1567478753, 'bc2cc2ccb2415507f74b739a869d9a26', 'super', 1, NULL, 1567478753);

ALTER TABLE `tree` ADD UNIQUE KEY `room_number` (`room_number`) USING BTREE;
ALTER TABLE `pay_order` ADD UNIQUE KEY `order_no` (`order_no`) USING BTREE;
ALTER TABLE `user` ADD UNIQUE KEY `username` (`username`) USING BTREE;
ALTER TABLE `admin_user` ADD UNIQUE KEY `username` (`username`) USING BTREE;




