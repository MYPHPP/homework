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

 Date: 21/01/2019 16:42:28
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
  `position` tinyint(3) UNSIGNED DEFAULT 1 COMMENT '展示位置 1：左侧菜单 2：添加按钮 3：编辑按钮',
  `icon` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图标',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of tp_menu
-- ----------------------------
INSERT INTO `tp_menu` VALUES (1, 1546845458, 1546845458, NULL, '后台首页', NULL, 0, NULL, 'bs/dashboard/index', 1, 'fa-home');
INSERT INTO `tp_menu` VALUES (2, 1546845458, 1546845458, NULL, '菜单管理', NULL, 0, NULL, '', 1, 'fa-list-ul');
INSERT INTO `tp_menu` VALUES (3, 1546845458, 1546845458, NULL, '菜单列表', NULL, 2, NULL, 'bs/menu/index', 1, 'fa-list-alt');
INSERT INTO `tp_menu` VALUES (4, 1548060123, 1548060123, NULL, '菜单添加', NULL, 3, 0, 'bs/menu/add', 2, 'fa-th-list');
INSERT INTO `tp_menu` VALUES (5, 1548060123, 1548060123, NULL, '菜单编辑', NULL, 2, 0, 'bs/menu/edit', 3, 'fa-th-list');

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
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of tp_role
-- ----------------------------
INSERT INTO `tp_role` VALUES (1, '超级管理员', '1,2,3,4,5,6,7,8', 1546845458, 1548056899, NULL);

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
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of tp_user
-- ----------------------------
INSERT INTO `tp_user` VALUES (1, 1546845458, 1546845458, NULL, 'admin', '14e1b600b1fd579f47433b88e8d85291', 1);

SET FOREIGN_KEY_CHECKS = 1;
