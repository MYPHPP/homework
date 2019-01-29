/*
 Navicat Premium Data Transfer

 Source Server         : tp51
 Source Server Type    : MySQL
 Source Server Version : 50642
 Source Host           : 192.168.33.233:3306
 Source Schema         : tp51

 Target Server Type    : MySQL
 Target Server Version : 50642
 File Encoding         : 65001

 Date: 29/01/2019 15:36:15
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tp_menu
-- ----------------------------
DROP TABLE IF EXISTS `tp_menu`;
CREATE TABLE `tp_menu`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_time` int(10) UNSIGNED DEFAULT NULL,
  `update_time` int(10) UNSIGNED DEFAULT NULL,
  `delete_time` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '标题',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '描述',
  `pid` int(10) UNSIGNED DEFAULT 0 COMMENT '父级id',
  `sort` int(10) UNSIGNED DEFAULT 0 COMMENT '排序',
  `route` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '路由地址',
  `position` tinyint(3) UNSIGNED DEFAULT 1 COMMENT '展示位置 1：左侧菜单 2：列表头部按3：列表底部 4：操作 5：单独方法',
  `icon` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图标',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of tp_menu
-- ----------------------------
INSERT INTO `tp_menu` VALUES (1, 1546845458, 1548312118, NULL, '后台首页', '东方大道', 0, 999, 'bs/dashboard/index', 1, 'fa-home');
INSERT INTO `tp_menu` VALUES (2, 1546845458, 1548311255, NULL, '菜单管理', NULL, 0, 10, '', 1, 'fa-bars');
INSERT INTO `tp_menu` VALUES (3, 1546845458, 1548311255, NULL, '菜单列表', NULL, 2, 0, 'bs/menu/index', 1, 'fa-list-alt');
INSERT INTO `tp_menu` VALUES (4, 1548060123, 1548311255, NULL, '菜单添加', NULL, 3, 0, 'bs/menu/add', 2, 'fa-th-list');
INSERT INTO `tp_menu` VALUES (5, 1548060123, 1548311255, NULL, '菜单编辑', NULL, 2, 0, 'bs/menu/edit', 4, 'fa-th-list');
INSERT INTO `tp_menu` VALUES (7, 1548063808, 1548311255, NULL, '批量删除', NULL, 3, 0, 'bs/menu/delAll', 3, 'fa-list-ul');
INSERT INTO `tp_menu` VALUES (8, 1548063822, 1548311255, NULL, '删除', NULL, 3, 0, 'bs/menu/delete', 4, 'fa-trash');
INSERT INTO `tp_menu` VALUES (9, 1548063866, 1548657805, NULL, '菜单管理', NULL, 2, 10, NULL, 5, 'fa-list-ul');
INSERT INTO `tp_menu` VALUES (10, 1548311810, 1548316129, NULL, '个人中心', NULL, 0, 0, 'bs/user/index', 1, 'fa-user');
INSERT INTO `tp_menu` VALUES (11, 1548658425, 1548658458, NULL, '权限管理', NULL, 0, 9, NULL, 1, 'fa-university');

-- ----------------------------
-- Table structure for tp_role
-- ----------------------------
DROP TABLE IF EXISTS `tp_role`;
CREATE TABLE `tp_role`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '角色名称',
  `access` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '角色权限',
  `create_time` int(10) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `update_time` int(10) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(10) UNSIGNED DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of tp_role
-- ----------------------------
INSERT INTO `tp_role` VALUES (1, '超级管理员', '1,2,3,4,5,6,7,8,9,10,11', 1546845458, 1548658425, NULL);

-- ----------------------------
-- Table structure for tp_user
-- ----------------------------
DROP TABLE IF EXISTS `tp_user`;
CREATE TABLE `tp_user`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `delete_time` int(11) DEFAULT NULL COMMENT '删除时间',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '用户名',
  `passwd` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '密码',
  `role_id` int(10) UNSIGNED DEFAULT NULL COMMENT '对应角色的主键',
  `status` tinyint(3) UNSIGNED DEFAULT NULL COMMENT '用户状态',
  `ip` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '登录ip地址',
  `lastlogin` int(10) UNSIGNED DEFAULT NULL COMMENT '上次登录时间',
  `locktime` int(10) UNSIGNED DEFAULT NULL COMMENT '超过登录次数之后锁的时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of tp_user
-- ----------------------------
INSERT INTO `tp_user` VALUES (1, 1546845458, 1548657012, NULL, 'admin', 'bac1df00/7MwxJhLtTD5HXfq2xFqci18kJwaw4e3uv/umN/QaG0', 1, NULL, '192.168.33.1', 2019, NULL);

SET FOREIGN_KEY_CHECKS = 1;
