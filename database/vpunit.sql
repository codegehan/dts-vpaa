/*
 Navicat Premium Data Transfer

 Source Server         : workbench_3308
 Source Server Type    : MySQL
 Source Server Version : 80035 (8.0.35)
 Source Host           : localhost:3308
 Source Schema         : vpunit

 Target Server Type    : MySQL
 Target Server Version : 80035 (8.0.35)
 File Encoding         : 65001

 Date: 31/01/2025 12:13:48
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for account_user
-- ----------------------------
DROP TABLE IF EXISTS `account_user`;
CREATE TABLE `account_user`  (
  `User_No` int NOT NULL AUTO_INCREMENT,
  `Firstname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `Middlename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `Lastname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `Suffix` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `Campus` int NULL DEFAULT NULL,
  `Department` int NULL DEFAULT NULL,
  `Account_Type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `Email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `Password` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `Date_Created` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `Date_Updated` datetime NULL DEFAULT NULL,
  `Last_Login` date NULL DEFAULT NULL,
  PRIMARY KEY (`User_No`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 15 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of account_user
-- ----------------------------
INSERT INTO `account_user` VALUES (1, 'admin', 'admin', 'admin', NULL, 1, 1, 'admin', 'admin@gmail.com', '3b612c75a7b5048a435fb6ec81e52ff92d6d795a8b5a9c17070f6a63c97a53b2', '2024-11-18 23:29:49', NULL, '2025-01-25');
INSERT INTO `account_user` VALUES (14, 'LUX LEIGH', 'NERI', 'APOS', '', 1, 3, 'staff', 'lux@gmail.com', '3b612c75a7b5048a435fb6ec81e52ff92d6d795a8b5a9c17070f6a63c97a53b2', '2025-01-07 08:33:48', '2025-01-25 09:22:18', '2025-01-25');

-- ----------------------------
-- Table structure for campus
-- ----------------------------
DROP TABLE IF EXISTS `campus`;
CREATE TABLE `campus`  (
  `Campus_No` int NOT NULL AUTO_INCREMENT,
  `Campus_Description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`Campus_No`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of campus
-- ----------------------------
INSERT INTO `campus` VALUES (1, 'Dapitan');

-- ----------------------------
-- Table structure for department
-- ----------------------------
DROP TABLE IF EXISTS `department`;
CREATE TABLE `department`  (
  `Department_No` int NOT NULL AUTO_INCREMENT,
  `Department_Description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `Campus` int NULL DEFAULT NULL,
  PRIMARY KEY (`Department_No`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of department
-- ----------------------------
INSERT INTO `department` VALUES (1, 'vpaa unit', 1);
INSERT INTO `department` VALUES (2, 'registrar', 1);
INSERT INTO `department` VALUES (3, 'guidance office', 1);

-- ----------------------------
-- Table structure for file_logs
-- ----------------------------
DROP TABLE IF EXISTS `file_logs`;
CREATE TABLE `file_logs`  (
  `File_Logs_No` int NOT NULL AUTO_INCREMENT,
  `Transaction_Code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `Receiving_Office` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `Action_By` int NULL DEFAULT NULL,
  `Status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'PENDING',
  `Note` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `Action_Date` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`File_Logs_No`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of file_logs
-- ----------------------------
INSERT INTO `file_logs` VALUES (1, 'DOC-110327', '1', NULL, 'PENDING', NULL, '2025-01-13 11:03:27');
INSERT INTO `file_logs` VALUES (2, 'DOC-113044', '1', NULL, 'PENDING', NULL, '2025-01-13 11:30:44');
INSERT INTO `file_logs` VALUES (3, 'DOC-113044', '1', 1, 'APPROVED', NULL, '2025-01-14 10:12:08');
INSERT INTO `file_logs` VALUES (4, 'DOC-110327', '1', 1, 'REJECTED', 'To test', '2025-01-14 10:12:22');
INSERT INTO `file_logs` VALUES (5, 'DOC-091322', '1', NULL, 'PENDING', NULL, '2025-01-25 09:13:22');
INSERT INTO `file_logs` VALUES (6, 'DOC-091322', '1', 1, 'APPROVED', NULL, '2025-01-25 09:17:33');
INSERT INTO `file_logs` VALUES (7, 'DOC-091946', '3', NULL, 'PENDING', NULL, '2025-01-25 09:19:46');
INSERT INTO `file_logs` VALUES (8, 'DOC-091946', '3', 14, 'APPROVED', NULL, '2025-01-25 09:21:26');

-- ----------------------------
-- Table structure for files
-- ----------------------------
DROP TABLE IF EXISTS `files`;
CREATE TABLE `files`  (
  `File_No` int NOT NULL AUTO_INCREMENT,
  `Transaction_Code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `Sender` int NULL DEFAULT NULL,
  `Receiving_Office` int NULL DEFAULT NULL,
  `Description` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `Purpose` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `Status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'PENDING',
  `Filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `Date_Created` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `Viewed_On` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`File_No`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of files
-- ----------------------------
INSERT INTO `files` VALUES (1, 'DOC-110327', 14, 1, 'MEMORANDUM OF AGGREEMENT FOR OJT IN COLLEGE OF COMPUTING STUDIES', 'TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST TEST ', 'REJECTED', 'MyResume_DOC-110327.pdf', '2025-01-13 11:03:27', '2025-01-13 11:28:32');
INSERT INTO `files` VALUES (2, 'DOC-113044', 14, 1, 'SAMPLE PARA NI LALA', 'PARA DI NA SIGEG REKLAMO', 'APPROVED', 'MyResume_DOC-113044.pdf', '2025-01-13 11:30:44', '2025-01-13 11:31:17');
INSERT INTO `files` VALUES (3, 'DOC-091322', 14, 1, 'LOVE NAKO SI LALA', 'WALA LANG LOVE LANG HIHI', 'APPROVED', 'Resume_DOC-091322.pdf', '2025-01-25 09:13:22', '2025-01-25 09:14:23');
INSERT INTO `files` VALUES (4, 'DOC-091842', 1, 1, 'SDASD', 'ASDASD', 'APPROVED', 'MyResume_DOC-091842.pdf', '2025-01-25 09:18:42', NULL);
INSERT INTO `files` VALUES (5, 'DOC-091946', 1, 3, 'DASDA', 'ADSDSDA', 'APPROVED', 'Resume_DOC-091946.pdf', '2025-01-25 09:19:46', '2025-01-25 09:20:05');

SET FOREIGN_KEY_CHECKS = 1;
