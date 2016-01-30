/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50539
Source Host           : localhost:3306
Source Database       : momic_m6

Target Server Type    : MYSQL
Target Server Version : 50539
File Encoding         : 65001

Date: 2016-01-29 12:14:20
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for mc_order_recall
-- ----------------------------
DROP TABLE IF EXISTS `oc_alipay_order`;
CREATE TABLE `oc_order_recall` (
  `alipay_order_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `trade_no` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `body` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `total_fee` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `trade_status` varchar(255) DEFAULT NULL,
  `buyer_email` varchar(255) DEFAULT NULL,
  `buyer_id` varchar(255) DEFAULT NULL,
  `seller_id` varchar(255) DEFAULT NULL,
  `gmt_create` datetime DEFAULT NULL,
  `notify_time` datetime DEFAULT NULL,
  `notify_type` varchar(255) DEFAULT NULL,
  `is_total_fee_adjust` varchar(255) DEFAULT NULL,
  `gmt_payment` datetime DEFAULT NULL,
  `payment_type` tinyint(3) DEFAULT NULL,
  `notify_id` varchar(255) DEFAULT NULL,
  `use_coupon` varchar(255) DEFAULT NULL,
  `sign_type` varchar(255) DEFAULT NULL,
  `sign` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`alipay_order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `oc_alipay_order_transaction`;

CREATE TABLE `oc_alipay_order_transaction` ( 
  `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `alipay_order_id` int(11) NOT NULL,
  `date_added` datetime DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`transaction_id`)
 
)ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
