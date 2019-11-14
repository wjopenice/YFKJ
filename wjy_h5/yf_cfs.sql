/*
 Navicat Premium Data Transfer

 Source Server         : 本地
 Source Server Type    : MySQL
 Source Server Version : 50723
 Source Host           : localhost:3306
 Source Schema         : yf_cfs

 Target Server Type    : MySQL
 Target Server Version : 50723
 File Encoding         : 65001

 Date: 03/09/2019 17:09:14
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for bill
-- ----------------------------
DROP TABLE IF EXISTS `bill`;
CREATE TABLE `bill`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户id',
  `currency_id` int(11) NOT NULL COMMENT '币种',
  `money` decimal(10, 3) NOT NULL COMMENT '红包金额',
  `order_no` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单号',
  `remark` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '备注信息',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 44 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for currency
-- ----------------------------
DROP TABLE IF EXISTS `currency`;
CREATE TABLE `currency`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NULL DEFAULT NULL COMMENT '排序',
  `name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '币种名称',
  `icon` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '图标地址',
  `tag` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '币种标识',
  `create_time` int(11) NOT NULL COMMENT '注册时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for dis_log
-- ----------------------------
DROP TABLE IF EXISTS `dis_log`;
CREATE TABLE `dis_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency_id` int(11) NOT NULL COMMENT '币种',
  `tree_id` int(11) NOT NULL DEFAULT 0 COMMENT '财富树id',
  `redpacket_id` int(11) NOT NULL DEFAULT 0 COMMENT '红包id',
  `uid` int(11) NOT NULL COMMENT '奖励/消费 用户 1为总部',
  `from_uid` int(11) NOT NULL COMMENT '来源用户id 0 为本人',
  `order_no` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单号',
  `level` tinyint(4) NOT NULL COMMENT '123',
  `scene` tinyint(1) NOT NULL COMMENT '1.财富树 2.红包',
  `money` decimal(10, 3) NOT NULL COMMENT '金额',
  `remark` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '备注',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pay_order
-- ----------------------------
DROP TABLE IF EXISTS `pay_order`;
CREATE TABLE `pay_order`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency_id` int(11) NULL DEFAULT NULL COMMENT '币种',
  `order_no` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单号',
  `uid` int(11) NOT NULL COMMENT '用户',
  `money` decimal(10, 2) NOT NULL COMMENT '用户',
  `state` tinyint(1) NOT NULL COMMENT '支付状态 1:待支付 2:已支付',
  `type` tinyint(1) NOT NULL COMMENT '用途 1:创建财富树 2:升级财富树 3.加入财富树 4:创建红包 6: 支付红包',
  `pay_time` int(11) NOT NULL COMMENT '支付时间',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`, `order_no`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 25 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for profit
-- ----------------------------
DROP TABLE IF EXISTS `profit`;
CREATE TABLE `profit`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '奖励用户 -1为总部',
  `from_uid` int(11) NOT NULL COMMENT '用户id',
  `scene` tinyint(4) NOT NULL COMMENT '来源类型:1.下级升级 2.群主收益 3.抢红包 ',
  `level` tinyint(4) NOT NULL COMMENT '0 1 2 3 分别代表本人 123级别',
  `rel_id` int(11) NOT NULL COMMENT '关联id',
  `money` decimal(10, 2) NOT NULL COMMENT '会员等级',
  `remark` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '收益金额',
  `create_time` int(11) NOT NULL COMMENT '升级时间',
  `currency_id` int(11) NULL DEFAULT NULL COMMENT '币种类型',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for red_collection
-- ----------------------------
DROP TABLE IF EXISTS `red_collection`;
CREATE TABLE `red_collection`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `redgroup_id` int(11) NOT NULL COMMENT '红包群id',
  `uid` int(11) NOT NULL COMMENT '用户',
  `istop` int(11) NOT NULL DEFAULT 0 COMMENT '是否置顶 0为没有置顶 1为置顶',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for red_comment
-- ----------------------------
DROP TABLE IF EXISTS `red_comment`;
CREATE TABLE `red_comment`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `redgroup_id` int(11) NOT NULL COMMENT '红包群id',
  `uid` int(11) NOT NULL COMMENT '用户',
  `reply_id` int(11) NOT NULL DEFAULT 0 COMMENT '回复评论的id',
  `parent_id` int(11) NOT NULL DEFAULT 0 COMMENT '主帖子id',
  `content` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '评论内容',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for red_log
-- ----------------------------
DROP TABLE IF EXISTS `red_log`;
CREATE TABLE `red_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency_id` int(11) NOT NULL COMMENT '币种',
  `redpacket_id` int(11) NOT NULL COMMENT '红包id',
  `uid` int(11) NOT NULL COMMENT '1为总部',
  `from_uid` int(11) NOT NULL COMMENT '来源用户id 0 为本人',
  `order_no` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单号',
  `scene` tinyint(4) NOT NULL COMMENT '1.发送红包 2.抢红包',
  `money` decimal(10, 3) NOT NULL COMMENT '金额',
  `remark` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '备注',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 36 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for red_zan
-- ----------------------------
DROP TABLE IF EXISTS `red_zan`;
CREATE TABLE `red_zan`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment_id` int(11) NOT NULL COMMENT '评论id',
  `uid` int(11) NOT NULL COMMENT '用户',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for redgroup
-- ----------------------------
DROP TABLE IF EXISTS `redgroup`;
CREATE TABLE `redgroup`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `currency_id` int(11) NOT NULL COMMENT '币种',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `money` decimal(10, 2) NOT NULL COMMENT '红包金额',
  `count` int(10) NOT NULL COMMENT '红包个数',
  `online_count` int(10) NOT NULL DEFAULT 0 COMMENT '在线人数',
  `room_number` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '房间号',
  `password` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '0' COMMENT '密码 0为没有密码',
  `order_no` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单号',
  `send_rule` tinyint(1) NOT NULL COMMENT '1.最少 2.最大',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `istop` int(11) NOT NULL DEFAULT 0 COMMENT '置顶 0不置顶 其他为置顶时间戳',
  `notice` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '群公告',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 38 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for redpacket
-- ----------------------------
DROP TABLE IF EXISTS `redpacket`;
CREATE TABLE `redpacket`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `redgroup_id` int(11) NOT NULL COMMENT '群id',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `money` decimal(10, 2) NOT NULL COMMENT '红包金额',
  `count` int(10) NOT NULL COMMENT '红包数量',
  `order_no` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单号',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 38 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for redpacket_log
-- ----------------------------
DROP TABLE IF EXISTS `redpacket_log`;
CREATE TABLE `redpacket_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `redpacket_id` int(11) NOT NULL COMMENT '群id',
  `uid` int(11) NULL DEFAULT 0 COMMENT '0标识没人 用户id',
  `money` decimal(10, 2) NOT NULL COMMENT '红包金额',
  `lucky` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0:不是下个发送者 1.下个发送者',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NULL DEFAULT 0 COMMENT '更新时间 及用户抢包时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 152 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for tree
-- ----------------------------
DROP TABLE IF EXISTS `tree`;
CREATE TABLE `tree`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_no` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '订单号',
  `room_number` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '房间号码',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `currency_id` int(11) NOT NULL COMMENT '币种',
  `level` tinyint(4) NOT NULL COMMENT '层级',
  `limit` tinyint(4) NOT NULL COMMENT '可推人数',
  `money` decimal(8, 2) NOT NULL COMMENT '加入价格',
  `growth_ratio` decimal(8, 2) NOT NULL COMMENT '增长比率',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for tree_log
-- ----------------------------
DROP TABLE IF EXISTS `tree_log`;
CREATE TABLE `tree_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency_id` int(11) NULL DEFAULT NULL COMMENT '币种',
  `tree_id` int(11) NULL DEFAULT NULL COMMENT '财富树id',
  `uid` int(11) NULL DEFAULT NULL COMMENT '奖励/消费 用户 -1为总部',
  `from_uid` int(11) NULL DEFAULT NULL COMMENT '来源用户id 0 为本人',
  `loss_uid` int(11) NULL DEFAULT NULL COMMENT '丢失人id 0为无',
  `order_no` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '订单号',
  `scene` tinyint(4) NULL DEFAULT NULL COMMENT '1.加入 2.升级',
  `money` decimal(10, 3) NULL DEFAULT NULL COMMENT '金额',
  `remark` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '备注',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for tree_upgrade
-- ----------------------------
DROP TABLE IF EXISTS `tree_upgrade`;
CREATE TABLE `tree_upgrade`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tree_id` int(11) NOT NULL COMMENT '财富树',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `level` tinyint(4) NOT NULL COMMENT '层级',
  `tolevel` tinyint(4) NOT NULL COMMENT '会员等级',
  `order_no` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '订单号',
  `money` decimal(10, 2) NOT NULL COMMENT '会员等级',
  `create_time` int(11) NOT NULL COMMENT '升级时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for tree_user
-- ----------------------------
DROP TABLE IF EXISTS `tree_user`;
CREATE TABLE `tree_user`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tree_id` int(11) NOT NULL COMMENT '财富树',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `puid` int(11) NULL DEFAULT NULL COMMENT '上级id',
  `level` tinyint(4) NOT NULL COMMENT '层级 0:代表群主 其他标识用于循环后做比较',
  `vip_level` tinyint(4) NOT NULL COMMENT '会员等级',
  `create_time` int(11) NOT NULL COMMENT '加入时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `order_no` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '订单号 如创建用户则无',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 57 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`  (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '账号',
  `password` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '密码',
  `nickname` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '昵称',
  `avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '头像',
  `wechat_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '微信二维码',
  `puid` int(11) NOT NULL COMMENT '上级id',
  `invite_code` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '邀请码',
  `token` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '登录token',
  `create_time` int(11) NOT NULL COMMENT '注册时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`uid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for wallet
-- ----------------------------
DROP TABLE IF EXISTS `wallet`;
CREATE TABLE `wallet`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NULL DEFAULT NULL COMMENT '用户id',
  `currency_id` int(11) NOT NULL COMMENT '币种id',
  `total` decimal(10, 3) NOT NULL DEFAULT 0.000 COMMENT '总金额',
  `free` decimal(10, 3) NOT NULL DEFAULT 0.000 COMMENT '可用',
  `lock` decimal(10, 3) NOT NULL DEFAULT 0.000 COMMENT '锁定',
  `consume` decimal(10, 3) NOT NULL DEFAULT 0.000 COMMENT '消费',
  `create_time` int(11) NOT NULL COMMENT '注册时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for y_group
-- ----------------------------
DROP TABLE IF EXISTS `y_group`;
CREATE TABLE `y_group`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` int(11) NOT NULL COMMENT '队长id',
  `users` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '成员id',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for y_user
-- ----------------------------
DROP TABLE IF EXISTS `y_user`;
CREATE TABLE `y_user`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '用户名',
  `userpass` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '密码',
  `money` decimal(10, 2) NULL DEFAULT NULL,
  `floor_id` int(11) UNSIGNED NULL DEFAULT 0 COMMENT '楼层ID',
  `create_time` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `login_time` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '登录时间',
  `sign` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '邀请码',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE COMMENT '账户唯一'
) ENGINE = InnoDB AUTO_INCREMENT = 38 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
