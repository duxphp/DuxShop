# ************************************************************
# Sequel Pro SQL dump
# Version 5438
#
# https://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.25)
# Database: duxshop
# Generation Time: 2019-05-27 02:06:03 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table dux_article
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_article`;

CREATE TABLE `dux_article` (
  `article_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `class_id` int(10) NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `title` varchar(250) DEFAULT '',
  `keyword` varchar(250) DEFAULT '',
  `description` varchar(250) DEFAULT '',
  `image` varchar(250) DEFAULT '',
  `auth` varchar(50) DEFAULT '',
  `content` text COMMENT '内容',
  `tags_id` varchar(250) DEFAULT '',
  `create_time` int(10) NOT NULL DEFAULT '0',
  `update_time` int(10) NOT NULL DEFAULT '0',
  `virtual_view` int(10) NOT NULL DEFAULT '0',
  `view` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`article_id`),
  KEY `class_id` (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_article_class
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_article_class`;

CREATE TABLE `dux_article_class` (
  `class_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) NOT NULL DEFAULT '0' COMMENT '上级栏目',
  `name` varchar(50) DEFAULT '',
  `subname` varchar(250) DEFAULT '',
  `image` varchar(250) DEFAULT '',
  `keyword` varchar(250) DEFAULT '',
  `description` varchar(250) DEFAULT '',
  `tpl_class` varchar(250) DEFAULT '',
  `tpl_content` varchar(250) DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`class_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_mall
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_mall`;

CREATE TABLE `dux_mall` (
  `mall_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `class_id` int(10) NOT NULL DEFAULT '0' COMMENT '分类ID',
  `brand_id` int(10) DEFAULT '0' COMMENT '品牌ID',
  `supplier_id` int(10) DEFAULT '0' COMMENT '供货商',
  `pos_id` varchar(10) DEFAULT '' COMMENT '推荐位ID',
  `type` varchar(250) NOT NULL DEFAULT 'mall',
  `title` varchar(250) DEFAULT '' COMMENT '标题',
  `subtitle` varchar(250) DEFAULT '' COMMENT '副标题',
  `image` varchar(250) DEFAULT '' COMMENT '封面图',
  `keyword` varchar(250) DEFAULT '' COMMENT '关键词',
  `description` varchar(250) DEFAULT '' COMMENT '描述',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `view` int(10) NOT NULL DEFAULT '0' COMMENT '浏览量',
  `tags_id` varchar(250) DEFAULT '' COMMENT 'TAGS',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `attr_new` tinyint(1) NOT NULL DEFAULT '0' COMMENT '属性-新品',
  `attr_hot` tinyint(1) NOT NULL DEFAULT '0' COMMENT '属性-热门',
  `attr_rec` tinyint(1) NOT NULL DEFAULT '0',
  `attr_free` tinyint(1) NOT NULL DEFAULT '0',
  `goods_no` varchar(50) DEFAULT '' COMMENT '商品编号',
  `sell_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '销售价',
  `market_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '市场价',
  `cost_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '成本价',
  `up_time` int(10) NOT NULL DEFAULT '0' COMMENT '上架时间',
  `down_time` int(10) NOT NULL DEFAULT '0' COMMENT '下架时间',
  `store` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '库存',
  `weight` int(10) NOT NULL DEFAULT '0' COMMENT '重量',
  `give_point` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '赠送积分',
  `images` text NOT NULL COMMENT '组图',
  `content` text NOT NULL COMMENT '详情',
  `unit` varchar(20) DEFAULT '' COMMENT '单位',
  `sale` int(10) NOT NULL DEFAULT '0' COMMENT '销量',
  `spec_data` text NOT NULL COMMENT '规格数据',
  `from_province` varchar(50) DEFAULT '' COMMENT '出产地',
  `from_city` varchar(50) DEFAULT '',
  `from_region` varchar(50) DEFAULT '',
  `favorite` int(10) NOT NULL DEFAULT '0' COMMENT '收藏量',
  `comments` int(10) NOT NULL DEFAULT '0' COMMENT '评论',
  `score` int(10) NOT NULL DEFAULT '0' COMMENT '评分',
  `freight_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '运费类型',
  `freight_tpl` int(10) NOT NULL DEFAULT '0' COMMENT '运费模板',
  `freight_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '固定运费',
  `service_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支持退换货',
  `point_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '购物送积分',
  `invoice_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '开具发票',
  `gift_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '赠品状态',
  `discount_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '会员折扣',
  `purchase_status` tinyint(1) NOT NULL DEFAULT '0',
  `purchase_limit` int(10) NOT NULL DEFAULT '0',
  `params` text,
  PRIMARY KEY (`mall_id`),
  KEY `class_id` (`class_id`),
  KEY `brand_id` (`brand_id`),
  KEY `attr_recommand` (`attr_new`),
  KEY `attr_hot` (`attr_hot`),
  KEY `update_time` (`update_time`),
  KEY `sale` (`sale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_mall_class
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_mall_class`;

CREATE TABLE `dux_mall_class` (
  `class_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) NOT NULL DEFAULT '0' COMMENT '上级栏目',
  `name` varchar(100) DEFAULT '' COMMENT '名称',
  `subname` varchar(100) DEFAULT '' COMMENT '副名称',
  `image` varchar(250) DEFAULT '' COMMENT '形象图',
  `keyword` varchar(250) DEFAULT '' COMMENT '关键词',
  `description` varchar(250) DEFAULT '' COMMENT '描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '顺序',
  PRIMARY KEY (`class_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_mall_order
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_mall_order`;

CREATE TABLE `dux_mall_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_mall_products
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_mall_products`;

CREATE TABLE `dux_mall_products` (
  `products_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '货品ID',
  `mall_id` int(10) NOT NULL DEFAULT '0' COMMENT '商品ID',
  `products_no` varchar(20) DEFAULT '' COMMENT '货号',
  `barcode` varchar(50) DEFAULT '' COMMENT '条形码',
  `spec_data` text COMMENT '规格属性',
  `sell_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '销售价',
  `market_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '市场价',
  `cost_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '成本价',
  `weight` int(10) NOT NULL DEFAULT '0' COMMENT '重量',
  `give_point` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '赠送积分',
  `store` int(10) NOT NULL DEFAULT '0' COMMENT '库存',
  `min_num` int(10) NOT NULL DEFAULT '1',
  `sale` int(10) NOT NULL DEFAULT '0' COMMENT '销量',
  PRIMARY KEY (`products_id`),
  KEY `mall_id` (`mall_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_marketing_coupon
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_marketing_coupon`;

CREATE TABLE `dux_marketing_coupon` (
  `coupon_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `class_id` int(10) NOT NULL DEFAULT '0',
  `type` varchar(20) DEFAULT '' COMMENT '类型',
  `has_id` varchar(250) DEFAULT '' COMMENT '关联ID',
  `rule` text COMMENT '优惠券规则',
  `url` varchar(250) DEFAULT '' COMMENT '使用链接',
  `name` varchar(250) DEFAULT '' COMMENT '名称',
  `image` varchar(250) DEFAULT '',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '面值',
  `meet_money` int(10) NOT NULL DEFAULT '0' COMMENT '满足费用',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `start_time` int(10) NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(10) NOT NULL DEFAULT '0' COMMENT '结束时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 0下架 1上架',
  `stock_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1不限量 0限量',
  `stock` int(10) NOT NULL DEFAULT '1' COMMENT '库存量',
  `receive` int(10) NOT NULL DEFAULT '0' COMMENT '领取量',
  `receive_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1限制领取',
  `expiry_day` int(10) NOT NULL DEFAULT '1' COMMENT '有效天数',
  `exchange_type` varchar(20) DEFAULT '' COMMENT '兑换方式',
  `exchange_price` int(10) NOT NULL DEFAULT '0' COMMENT '兑换价格',
  `del_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '删除状态',
  PRIMARY KEY (`coupon_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_marketing_coupon_class
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_marketing_coupon_class`;

CREATE TABLE `dux_marketing_coupon_class` (
  `class_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT '' COMMENT '分类名',
  `sort` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_marketing_coupon_gift
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_marketing_coupon_gift`;

CREATE TABLE `dux_marketing_coupon_gift` (
  `gift_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `coupon_id` int(10) NOT NULL DEFAULT '0',
  `name` varchar(250) DEFAULT '',
  `start_time` int(10) NOT NULL DEFAULT '0',
  `stop_time` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`gift_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_marketing_coupon_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_marketing_coupon_log`;

CREATE TABLE `dux_marketing_coupon_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `coupon_id` int(10) NOT NULL DEFAULT '0' COMMENT '优惠券ID',
  `start_time` int(10) NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_time` int(10) NOT NULL DEFAULT '0' COMMENT '结束时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '使用状态',
  `del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '用户删除',
  `show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '首次显示',
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`),
  KEY `coupon_id` (`coupon_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_marketing_coupon_rec
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_marketing_coupon_rec`;

CREATE TABLE `dux_marketing_coupon_rec` (
  `rec_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `image` varchar(250) DEFAULT '',
  `new_coupon_id` int(10) NOT NULL DEFAULT '0',
  `old_coupon_id` int(10) NOT NULL DEFAULT '0',
  `start_time` int(10) NOT NULL DEFAULT '0',
  `stop_time` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `name` varchar(250) DEFAULT '',
  `description` varchar(250) DEFAULT '',
  `content` text,
  PRIMARY KEY (`rec_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_marketing_coupon_rec_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_marketing_coupon_rec_log`;

CREATE TABLE `dux_marketing_coupon_rec_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0',
  `rec_user_id` int(10) NOT NULL DEFAULT '0',
  `rec_id` int(10) NOT NULL DEFAULT '0',
  `time` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_member_config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_member_config`;

CREATE TABLE `dux_member_config` (
  `config_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT '',
  `content` text,
  `description` varchar(250) DEFAULT '',
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `dux_member_config` WRITE;
/*!40000 ALTER TABLE `dux_member_config` DISABLE KEYS */;

INSERT INTO `dux_member_config` (`config_id`, `name`, `content`, `description`)
VALUES
	(1,'reg_status','1','开放注册'),
	(2,'reg_ban_name','','禁止使用名称'),
	(3,'reg_ban_ip','','禁止注册IP'),
	(4,'reg_agreement','&lt;p style=&quot;text-align: left;&quot; align=&quot;left&quot;&gt;&lt;span lang=&quot;EN-US&quot; style=&quot;font-size: 12.0pt; font-family: 宋体;&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: left; vertical-align: middle;&quot; align=&quot;left&quot;&gt;&amp;nbsp;&lt;/p&gt;','注册协议'),
	(5,'verify_status','1','验证码状态'),
	(7,'reg_check','1','注册审核'),
	(8,'verify_second','60','发送间隔'),
	(9,'verify_minute','2','限制分钟'),
	(10,'verify_minute_num','1','限制条数'),
	(11,'verify_expire','120','时效秒'),
	(13,'reg_role','1','注册用户组'),
	(14,'reg_type','tel','帐号类型'),
	(15,'reg_info','技术支持：某铺社区团购 www.moupu.com','关于会员'),
	(16,'verify_sms_tpl','a:2:{s:2:\"id\";s:13:\"SMS_160170073\";s:4:\"data\";a:2:{s:3:\"key\";a:2:{i:0;s:9:\"验证码\";i:1;s:9:\"有效期\";}s:3:\"val\";a:2:{i:0;s:4:\"code\";i:1;s:6:\"expire\";}}}','短信验证码模板'),
	(20,'verify_mail_tpl','&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;亲爱的会员您好！ 请在验证码输入框中输入：&lt;span style=&quot;font-size: 14pt; color: #e74c3c;&quot;&gt; [验证码]&lt;/span&gt;，以完成操作，验证码有效期&lt;span style=&quot;color: #3598db; font-size: 14pt;&quot;&gt;[有效期]&lt;/span&gt;分钟。 &lt;/strong&gt;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: center;&quot;&gt;注意：此操作可能会修改您的密码、登录邮箱或绑定手机。如非本人操作，请及时登录并修改密码以保证帐户安全&lt;br /&gt;（工作人员不会向你索取此验证码，请勿泄漏！) &lt;/p&gt;','邮件验证码模板'),
	(21,'clear_withdraw','1','提现额度'),
	(22,'clear_tax','1','提现手续费'),
	(23,'clear_num','2','当月提现次数'),
	(24,'notice_recharge_status','1',''),
	(25,'notice_recharge_class','a:2:{i:0;s:4:\"mail\";i:1;s:6:\"wechat\";}',''),
	(26,'notice_recharge_title','充值成功',''),
	(27,'notice_recharge_sms_tpl','恭喜您充值[充值金额]元成功',''),
	(28,'notice_recharge_mail_tpl','',''),
	(29,'notice_recharge_wechat_tpl','',''),
	(30,'verify_image','1','图形验证码状态'),
	(31,'recharge_offline','1',''),
	(32,'recharge_offline_info','中国工商银行 xxx 600000000000',''),
	(33,'程。</p>\n\n<p>第十七条_店铺装修区，指店铺招牌、商品分类、公告栏、促销区、广告牌等店铺相关模块。</p>\n\n<p>第十八条_成交，指买家在品真多上拍下商品并成功付款到支付宝。货到付款交易中买家拍下商品即视为成交。</p>\n\n<p>第十九条_下架，指将出售中的商品转移至线上仓库。</p>\n\n<p>第二十条_包邮，指卖家对所售商品承诺在其指定的地区内向买家承担首次发货运费。</p>\n\n<p><span_style=\"font-family:&quot;微软雅黑&quot;,&quot;san','',''),
	(34,'5','',''),
	(35,'clear_type','a:3:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";}',''),
	(36,'clear_audit','a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}',''),
	(37,'clear_tax_free','1000',''),
	(38,'clear_withdraw_max','5000',''),
	(39,'clear_info','&lt;p&gt;用户提现说明&lt;/p&gt;','');

/*!40000 ALTER TABLE `dux_member_config` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_member_connect
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_member_connect`;

CREATE TABLE `dux_member_connect` (
  `connect_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `union_id` varchar(100) DEFAULT '',
  `open_id` varchar(100) DEFAULT '' COMMENT '平台ID',
  `token` varchar(250) DEFAULT '' COMMENT '密钥',
  `type` varchar(10) DEFAULT '' COMMENT '类型',
  `data` text COMMENT '数据',
  `follow` tinyint(1) NOT NULL DEFAULT '0' COMMENT '关注',
  PRIMARY KEY (`connect_id`),
  KEY `user_id` (`user_id`),
  KEY `open_id` (`open_id`),
  KEY `token` (`token`(191)),
  KEY `type` (`type`),
  KEY `union_id` (`union_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_member_feedback
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_member_feedback`;

CREATE TABLE `dux_member_feedback` (
  `feedback_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `time` int(10) NOT NULL DEFAULT '0',
  `user_id` int(10) NOT NULL DEFAULT '0',
  `content` text,
  PRIMARY KEY (`feedback_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_member_file
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_member_file`;

CREATE TABLE `dux_member_file` (
  `file_id` int(10) NOT NULL AUTO_INCREMENT,
  `url` varchar(250) DEFAULT '',
  `original` varchar(250) DEFAULT '',
  `title` varchar(250) DEFAULT '',
  `ext` varchar(20) DEFAULT '',
  `size` int(10) NOT NULL DEFAULT '0',
  `time` int(10) NOT NULL DEFAULT '0',
  `user_id` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`file_id`),
  KEY `ext` (`ext`),
  KEY `time` (`time`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='上传文件';



# Dump of table dux_member_grade
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_member_grade`;

CREATE TABLE `dux_member_grade` (
  `grade_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT '' COMMENT '等级名称',
  `subname` varchar(20) DEFAULT '' COMMENT '等级代号',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '等级顺序',
  `discount` int(10) NOT NULL DEFAULT '0',
  `update_status` tinyint(1) NOT NULL DEFAULT '0',
  `update_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`grade_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `dux_member_grade` WRITE;
/*!40000 ALTER TABLE `dux_member_grade` DISABLE KEYS */;

INSERT INTO `dux_member_grade` (`grade_id`, `name`, `subname`, `sort`, `discount`, `update_status`, `update_money`)
VALUES
	(1,'普通会员','LV0',0,0,0,0.00);

/*!40000 ALTER TABLE `dux_member_grade` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_member_notice
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_member_notice`;

CREATE TABLE `dux_member_notice` (
  `notice_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0',
  `time` int(10) NOT NULL DEFAULT '0',
  `icon` varchar(50) DEFAULT '' COMMENT '图标',
  `content` text COMMENT '内容',
  `url` varchar(250) DEFAULT '' COMMENT '链接',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '查看状态',
  PRIMARY KEY (`notice_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_member_real
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_member_real`;

CREATE TABLE `dux_member_real` (
  `real_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0',
  `name` varchar(250) DEFAULT '' COMMENT '姓名',
  `idcard` varchar(50) DEFAULT '' COMMENT '身份证号码',
  `card_image` varchar(255) DEFAULT '' COMMENT '身份证正面',
  `card_image_back` varchar(255) DEFAULT '' COMMENT '身份证反面',
  `card_image_hand` varchar(255) DEFAULT '' COMMENT '手持身份证照片',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `time` int(10) NOT NULL DEFAULT '0' COMMENT '提交时间',
  `remark` text COMMENT '审核备注',
  PRIMARY KEY (`real_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_member_role
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_member_role`;

CREATE TABLE `dux_member_role` (
  `role_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT '' COMMENT '角色名',
  `description` varchar(50) DEFAULT '' COMMENT '描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `dux_member_role` WRITE;
/*!40000 ALTER TABLE `dux_member_role` DISABLE KEYS */;

INSERT INTO `dux_member_role` (`role_id`, `name`, `description`, `status`)
VALUES
	(1,'会员','普通默认用户',1);

/*!40000 ALTER TABLE `dux_member_role` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_member_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_member_user`;

CREATE TABLE `dux_member_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) NOT NULL DEFAULT '0',
  `grade_id` int(10) NOT NULL DEFAULT '1',
  `nickname` varchar(50) DEFAULT '',
  `email` varchar(100) DEFAULT '',
  `tel` varchar(20) DEFAULT '',
  `password` varchar(250) DEFAULT '',
  `avatar` varchar(250) DEFAULT '',
  `province` varchar(50) DEFAULT '',
  `city` varchar(50) DEFAULT '',
  `region` varchar(50) DEFAULT '',
  `reg_time` int(10) NOT NULL DEFAULT '0',
  `login_time` int(10) NOT NULL DEFAULT '0',
  `login_ip` varchar(200) DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sex` tinyint(1) NOT NULL DEFAULT '1' COMMENT '性别',
  PRIMARY KEY (`user_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_member_verify
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_member_verify`;

CREATE TABLE `dux_member_verify` (
  `verify_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `receive` varchar(60) DEFAULT '' COMMENT '接收方',
  `code` varchar(20) DEFAULT '' COMMENT '验证码',
  `time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `type` int(10) NOT NULL DEFAULT '0' COMMENT '信道',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `expire` int(10) NOT NULL DEFAULT '1800' COMMENT '有效期',
  PRIMARY KEY (`verify_id`),
  KEY `receive` (`receive`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_order
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_order`;

CREATE TABLE `dux_order` (
  `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单ID',
  `order_app` varchar(50) DEFAULT '' COMMENT '商品应用',
  `order_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '订单用户',
  `order_no` varchar(20) DEFAULT '' COMMENT '订单号',
  `order_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单金额',
  `order_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '订单状态',
  `order_sum` int(10) NOT NULL DEFAULT '0' COMMENT '商品总数',
  `order_title` varchar(250) DEFAULT '' COMMENT '订单描述',
  `order_image` varchar(250) DEFAULT '' COMMENT '订单图片',
  `order_create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `order_complete_time` int(10) NOT NULL DEFAULT '0' COMMENT '完成时间',
  `order_close_time` int(10) NOT NULL DEFAULT '0' COMMENT '取消时间',
  `order_complete_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '完成状态',
  `order_ip` varchar(250) DEFAULT '' COMMENT '下单IP',
  `order_remark` varchar(250) DEFAULT '' COMMENT '订单备注',
  `order_coupon` varchar(100) DEFAULT '' COMMENT '优惠券',
  `receive_name` varchar(50) DEFAULT '' COMMENT '收件人',
  `receive_tel` varchar(20) DEFAULT '' COMMENT '收件电话',
  `receive_province` varchar(100) DEFAULT '' COMMENT '收件地区',
  `receive_city` varchar(100) DEFAULT '',
  `receive_region` varchar(100) DEFAULT '',
  `receive_street` varchar(100) DEFAULT '',
  `receive_school` varchar(100) DEFAULT '',
  `receive_floor` varchar(100) DEFAULT '',
  `receive_address` varchar(250) DEFAULT '' COMMENT '收件地址',
  `receive_zip` varchar(50) DEFAULT '' COMMENT '收件邮编',
  `pay_currency` text COMMENT '货币支付',
  `pay_discount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠总价',
  `pay_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单支付',
  `pay_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '付款方式(1在线 0货到)',
  `pay_data` text COMMENT '付款信息',
  `pay_time` int(10) NOT NULL DEFAULT '0' COMMENT '付款时间',
  `pay_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '付款状态',
  `discount_coupon` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠券折扣',
  `discount_user` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '会员折扣',
  `parcel_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '配货状态',
  `comment_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '评论状态',
  `take_id` int(10) NOT NULL DEFAULT '0' COMMENT '配送点',
  `take_type` int(10) NOT NULL DEFAULT '0' COMMENT '配送类型',
  `delivery_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '发货状态(1发货 0未发货)',
  `delivery_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '运费',
  `refund_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '退款金额',
  `stockout_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`order_id`),
  KEY `order_app` (`order_app`),
  KEY `order_user_id` (`order_user_id`),
  KEY `order_complete_status` (`order_complete_status`),
  KEY `pay_type` (`pay_type`),
  KEY `pay_status` (`pay_status`),
  KEY `parcel_status` (`parcel_status`),
  KEY `comment_status` (`comment_status`),
  KEY `delivery_status` (`delivery_status`),
  KEY `take_id` (`take_id`),
  KEY `order_status` (`order_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_order_address
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_order_address`;

CREATE TABLE `dux_order_address` (
  `add_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `store` varchar(100) DEFAULT '' COMMENT '店名',
  `name` varchar(50) DEFAULT '' COMMENT '姓名',
  `tel` varchar(20) DEFAULT '' COMMENT '电话',
  `province` varchar(100) DEFAULT '' COMMENT '省份',
  `city` varchar(100) DEFAULT '' COMMENT '城市',
  `region` varchar(100) DEFAULT '' COMMENT '地区',
  `street` varchar(250) DEFAULT '',
  `school` varchar(250) DEFAULT '',
  `floor` varchar(250) DEFAULT '',
  `address` varchar(250) DEFAULT '' COMMENT '详细地址',
  `zip` int(10) NOT NULL DEFAULT '0' COMMENT '邮编',
  `default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '默认',
  PRIMARY KEY (`add_id`),
  KEY `user_id` (`user_id`),
  KEY `default` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_order_cart
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_order_cart`;

CREATE TABLE `dux_order_cart` (
  `cart_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `data` text COMMENT '数据',
  PRIMARY KEY (`cart_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_order_comment
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_order_comment`;

CREATE TABLE `dux_order_comment` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_goods_id` int(10) NOT NULL DEFAULT '0' COMMENT '订单商品ID',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `app` varchar(20) DEFAULT '' COMMENT '应用名',
  `has_id` int(10) NOT NULL DEFAULT '0' COMMENT '关联ID',
  `spec` text COMMENT '商品属性',
  `time` int(10) NOT NULL DEFAULT '0' COMMENT '评价时间',
  `content` text COMMENT '评价内容',
  `level` tinyint(1) NOT NULL DEFAULT '3' COMMENT '评价分数',
  `images` text COMMENT '评论图片',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '审核状态',
  PRIMARY KEY (`comment_id`),
  KEY `order_goods_id` (`order_goods_id`),
  KEY `user_id` (`user_id`),
  KEY `app` (`app`),
  KEY `has_id` (`has_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_order_config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_order_config`;

CREATE TABLE `dux_order_config` (
  `config_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT '',
  `content` text,
  `description` varchar(250) DEFAULT '',
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `dux_order_config` WRITE;
/*!40000 ALTER TABLE `dux_order_config` DISABLE KEYS */;

INSERT INTO `dux_order_config` (`config_id`, `name`, `content`, `description`)
VALUES
	(1,'waybill_type','kdniao','快递查询接口'),
	(14,'notice_status','1',''),
	(15,'notice_pay_class','a:3:{i:0;s:3:\"sms\";i:1;s:3:\"app\";i:2;s:6:\"wechat\";}',''),
	(16,'notice_pay_title','您有新的订单已付款',''),
	(18,'notice_pay_status','1',''),
	(19,'notice_pay_sms_tpl','[ID:SMS_117517965]您有新的订单已付款，请耐心等待发货',''),
	(20,'notice_pay_mail_tpl','&lt;p&gt;&lt;strong style=&quot;display:block;margin-bottom:15px;&quot;&gt;&lt;font size=&quot;3&quot;&gt;亲爱的会员您好！ &lt;/font&gt;&lt;/strong&gt; &lt;strong style=&quot;display:block;margin-bottom:15px;&quot;&gt;&lt;font size=&quot;3&quot;&gt; 您订购的商品【[订单标题]】已经付款，请您耐心等待发货。&lt;/font&gt;&lt;/strong&gt;&lt;/p&gt;\n\n&lt;p&gt;订单号：[订单编号]&lt;/p&gt;\n\n&lt;p&gt;支付号：[支付号]&lt;/p&gt;\n\n&lt;p&gt;支付金额：[支付金额]&lt;/p&gt;\n\n&lt;p&gt;支付时间：[支付时间]&lt;/p&gt;\n\n&lt;div style=&quot;margin-bottom:30px;&quot;&gt;\n&lt;p style=&quot;color:#747474;&quot;&gt;&lt;small style=&quot;display:block;margin-bottom:20px;font-size:12px;&quot;&gt;如有问题请及时跟我们工作人员联系，谨防诈骗电话。&lt;/small&gt;&lt;/p&gt;\n&lt;/div&gt;\n\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\n',''),
	(21,'notice_pay_wechat_tpl','订单号：[订单编号]\n商品名称：[订单标题] \n订单金额：[订单金额]\n支付时间：[支付时间]',''),
	(22,'notice_delivery_status','1',''),
	(23,'notice_delivery_class','a:3:{i:0;s:3:\"sms\";i:1;s:3:\"app\";i:2;s:6:\"wechat\";}',''),
	(24,'notice_delivery_title','您订购的商品已发货，请注意收货',''),
	(25,'notice_delivery_sms_tpl','[ID:SMS_117512911]您订购的商品【[订单标题]】已发货，请注意收货',''),
	(26,'notice_delivery_mail_tpl','&lt;p&gt;&lt;strong style=&quot;display:block;margin-bottom:15px;&quot;&gt;&lt;font size=&quot;3&quot;&gt;亲爱的会员您好！ &lt;/font&gt;&lt;/strong&gt; &lt;strong style=&quot;display:block;margin-bottom:15px;&quot;&gt;&lt;font size=&quot;3&quot;&gt; 您订购的商品【[订单标题]】已发货，请注意收货。&lt;/font&gt;&lt;/strong&gt;&lt;/p&gt;\n\n&lt;p&gt;订单号：[订单编号]&lt;/p&gt;\n\n&lt;p&gt;快递名称：[快递名称]&lt;/p&gt;\n\n&lt;p&gt;快递单号：[快递单号]&lt;/p&gt;\n\n&lt;div style=&quot;margin-bottom:30px;&quot;&gt;\n&lt;p style=&quot;color:#747474;&quot;&gt;&lt;small style=&quot;display:block;margin-bottom:20px;font-size:12px;&quot;&gt;如有问题请及时跟我们工作人员联系，谨防诈骗电话。&lt;/small&gt;&lt;/p&gt;\n&lt;/div&gt;\n\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\n',''),
	(27,'notice_delivery_wechat_tpl','订单号：[订单编号]\n订单名称：[订单标题] \n快递名称：[快递名称]\n快递单号：[快递单号]',''),
	(28,'notice_complete_status','1',''),
	(29,'notice_complete_class','a:3:{i:0;s:3:\"sms\";i:1;s:3:\"app\";i:2;s:6:\"wechat\";}',''),
	(30,'notice_complete_title','您购买得商品已确认完成，感谢您的光临',''),
	(31,'notice_complete_sms_tpl','[ID:SMS_117523019]请在订购的商品【[订单标题]】已经确认收货，感谢您的再次光临。',''),
	(32,'notice_complete_mail_tpl','&lt;p&gt;&lt;strong style=&quot;display:block;margin-bottom:15px;&quot;&gt;&lt;font size=&quot;3&quot;&gt;亲爱的会员您好！ &lt;/font&gt;&lt;/strong&gt; &lt;strong style=&quot;display:block;margin-bottom:15px;&quot;&gt;&lt;font size=&quot;3&quot;&gt; 您在订购的商品【[订单标题]】已经确认收货，感谢您的再次光临。&lt;/font&gt;&lt;/strong&gt;&lt;/p&gt;\n\n&lt;p&gt;订单号：[订单编号]&lt;/p&gt;\n\n&lt;p&gt;下单时间：[下单时间]&lt;/p&gt;\n\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\n\n&lt;p&gt;确认时间：[确认时间]&lt;/p&gt;\n\n&lt;div style=&quot;margin-bottom:30px;&quot;&gt;\n&lt;p style=&quot;color:#747474;&quot;&gt;&lt;small style=&quot;display:block;margin-bottom:20px;font-size:12px;&quot;&gt;如有问题请及时跟我们工作人员联系，谨防诈骗电话。&lt;/small&gt;&lt;/p&gt;\n&lt;/div&gt;\n\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\n',''),
	(33,'notice_complete_wechat_tpl','订单号：[订单编号]\n订单名称：[订单标题]\n下单时间：[下单时间]\n确认时间：[确认时间]',''),
	(34,'cod_status','0',''),
	(35,'amount_status','1',''),
	(36,'contact_name','某铺',''),
	(37,'contact_tel','18000000000',''),
	(38,'contact_province','湖南省',''),
	(39,'contact_city','长沙市',''),
	(41,'contact_address','合能璞丽',''),
	(42,'contact_zip','421000',''),
	(44,'contact_region','岳麓区',''),
	(45,'notice_pay_app_tpl','您的订单已经支付，请注意查收',''),
	(46,'notice_delivery_app_tpl','您的订单已经发货，请注意查收',''),
	(47,'notice_complete_app_tpl','订单已完成',''),
	(48,'notice_pay_site_tpl','',''),
	(49,'notice_delivery_site_tpl','',''),
	(50,'notice_complete_site_tpl','',''),
	(51,'service_day','15',''),
	(52,'coupon_auth_status','1',''),
	(53,'confirm_day','7',''),
	(54,'cancel_hour','30',''),
	(55,'pay_type','0',''),
	(56,'pay_tip','请您在收到货物时在订单详情进行支付',''),
	(57,'pay_agreement','&lt;p&gt;您确认，在您申请使用淘宝（中国）有限公司（以下简称“淘宝”）和支付宝（中国）网络技术有限公司（以下简称“支付宝”）联合提供给您的货到付款服务（以下简称“本服务”）前，您已经充分阅读本协议内容，一旦您在淘宝网站平台签约页面点击“同意并申请服务”按钮，即表示您对本协议内容完全理解并同意接受本协议所有条款的约定，本协议即对您和支付宝、淘宝产生法律约束力。&lt;/p&gt;\r\n&lt;p&gt;您签订本协议的前提是您已经完全理解并接受您与支付宝之间签订的《支付宝服务协议》及相应的规则中对本服务的约定。&lt;/p&gt;\r\n&lt;p&gt;您同意并接受，您在使用本服务时，您只能在淘宝物流平台选择物流公司为您提供货物运送服务。但相关物流公司公示的物流价格或单独向您明示的物流价格是该物流公司自身的行为，对此需要您与该物流公司单独协商确定最终的物流价格，支付宝和淘宝不对此提供任何形式的担保和承诺。&lt;/p&gt;\r\n&lt;p&gt;基于本服务本身的特殊性，您理解并同意，在您向您的交易对方发送交易货物后，您能否收到来自交易对方的付款，取决于您委托的物流公司能够送达收货地址、您交付运送的货物是否符合您与交易对方的约定、您的交易对方是否同意签收付款，支付宝和淘宝对您使用本服务时能否收到交易货款不提供任何形式的担保和承诺。如因上述因素导致您未收到或未及时收到交易货款，您应当自行承担相应后果（包括但不限于运费的承担）。&lt;/p&gt;\r\n&lt;p&gt;在您使用本服务的过程中，您收取交易货款的唯一依据是您的交易对方向物流公司支付了交易货款。支付宝将在物流公司向“支付宝”软件系统中声称，其已收到您的交易对方支付的交易货款之日起的第三日向您支付交易货款。您理解并同意，物流公司自收到货款至在“支付宝”软件系统中声称已经收到交易货款是需要时间的，且该时间完全取决于物流公司的内部运作方式。因此，您不得以买家已实际付款、物流公司应该可以完成声称行为等理由向支付宝或淘宝主张要求支付交易货款。&lt;/p&gt;\r\n&lt;p&gt;您同意并接受，使用本服务您应当向支付宝支付服务费用，服务费用按照卖家最后成交，进行收取每笔交易款项(包含运费)的1%收取，且按照四舍五入原则按单位“元”取整数，交易不成功，不收取服务费用。另，商城用户订购该服务不收取服务费用。&lt;/p&gt;\r\n&lt;p&gt;您使用本服务，可以选择由您向物流公司支付货到付款服务费用，并且对商品做包邮设置时(如买家承担运费，满就包邮等)需要由您承担货到付款服务费用。服务费用的具体收取标准按照淘宝页面实时公布的信息为准，交易不&lt;/p&gt;\r\n&lt;p&gt;成功，不收取服务费用。&lt;/p&gt;\r\n&lt;p&gt;您同意并接&lt;/p&gt;\r\n&lt;p&gt;受，卖家承担服务费时，如果买家签收后发现货物质量等问题，发起退货维权，维权成立则卖家需按商品价格退款给买家。&lt;/p&gt;\r\n&lt;p&gt;因快递代收款需按单位“元”取整，由您向物流公司支付货到付款服务费时，快递代收款取整将舍掉零头。你同意并接受，该舍掉的零头将作为货到付款优惠从您的交易货款中扣除。&lt;/p&gt;',''),
	(58,'pos_type','1',''),
	(59,'pos_tpl','1','');

/*!40000 ALTER TABLE `dux_order_config` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_order_config_delivery
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_order_config_delivery`;

CREATE TABLE `dux_order_config_delivery` (
  `delivery_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '' COMMENT '模板名称',
  `first_weight` int(10) NOT NULL DEFAULT '1000' COMMENT '首重重量',
  `first_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '首重价格',
  `second_weight` int(10) NOT NULL DEFAULT '1000' COMMENT '续重重量',
  `second_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '续重价格',
  `support_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '报价支持',
  `support_rate` int(10) NOT NULL DEFAULT '0' COMMENT '保价费率',
  `support_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '最低保价',
  `area` text COMMENT '配送地区',
  `tpl` text COMMENT '快递模板',
  PRIMARY KEY (`delivery_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `dux_order_config_delivery` WRITE;
/*!40000 ALTER TABLE `dux_order_config_delivery` DISABLE KEYS */;

INSERT INTO `dux_order_config_delivery` (`delivery_id`, `name`, `first_weight`, `first_price`, `second_weight`, `second_price`, `support_status`, `support_rate`, `support_price`, `area`, `tpl`)
VALUES
	(1,'默认模板',1000,10.00,1000,5.00,0,0,0.00,'','');

/*!40000 ALTER TABLE `dux_order_config_delivery` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_order_config_express
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_order_config_express`;

CREATE TABLE `dux_order_config_express` (
  `express_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '' COMMENT '物流名称',
  `logo` varchar(250) DEFAULT '' COMMENT '物流LOGO',
  `label` varchar(50) DEFAULT '' COMMENT '物流标识',
  `url` varchar(250) DEFAULT '' COMMENT '物流网址',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '顺序',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `place_status` tinyint(1) NOT NULL DEFAULT '0',
  `customer_name` varchar(250) DEFAULT '',
  `customer_pwd` varchar(250) DEFAULT '',
  `month_code` varchar(250) DEFAULT '',
  `send_site` varchar(250) DEFAULT '',
  `send_staff` varchar(250) DEFAULT '',
  `ware_house_id` varchar(250) DEFAULT '',
  `ware_name` varchar(250) DEFAULT '',
  PRIMARY KEY (`express_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `dux_order_config_express` WRITE;
/*!40000 ALTER TABLE `dux_order_config_express` DISABLE KEYS */;

INSERT INTO `dux_order_config_express` (`express_id`, `name`, `logo`, `label`, `url`, `sort`, `status`, `place_status`, `customer_name`, `customer_pwd`, `month_code`, `send_site`, `send_staff`, `ware_house_id`, `ware_name`)
VALUES
	(3,'圆通快递','','YTO','',0,1,1,'','','','','','',''),
	(4,'顺丰','','SF','',0,1,0,NULL,'',NULL,NULL,NULL,NULL,NULL),
	(5,'百世快递','','HTKY','',0,1,0,NULL,'',NULL,NULL,NULL,NULL,NULL),
	(6,'中通快递','','ZTO','https://www.zto.com/',0,1,1,'','','','','','',''),
	(7,'申通快递','','STO','',0,1,0,NULL,'',NULL,NULL,NULL,NULL,NULL),
	(8,'韵达速递','','YD','',0,1,0,NULL,'',NULL,NULL,NULL,NULL,NULL),
	(9,'邮政快递包裹','','YZPY','',0,1,0,NULL,'',NULL,NULL,NULL,NULL,NULL),
	(10,'EMS','','EMS','',0,1,0,NULL,'',NULL,NULL,NULL,NULL,NULL),
	(11,'天天快递','','HHTT','',0,1,0,NULL,'',NULL,NULL,NULL,NULL,NULL),
	(12,'京东物流','','JD','',0,1,0,NULL,'',NULL,NULL,NULL,NULL,NULL),
	(13,'优速快递','','UC','',0,1,0,NULL,'',NULL,NULL,NULL,NULL,NULL),
	(14,'德邦','','DBL','',0,1,0,NULL,'',NULL,NULL,NULL,NULL,NULL),
	(15,'快捷快递','','FAST','',0,1,0,NULL,'',NULL,NULL,NULL,NULL,NULL),
	(16,'宅急送','','ZJS','',0,1,0,NULL,'',NULL,NULL,NULL,NULL,NULL),
	(17,'TNT快递','','TNT','',0,1,0,NULL,'',NULL,NULL,NULL,NULL,NULL),
	(18,'UPS','','UPS','',0,1,0,NULL,'',NULL,NULL,NULL,NULL,NULL),
	(19,'DHL','','DHL','',0,1,0,NULL,'',NULL,NULL,NULL,NULL,NULL),
	(20,'FEDEX联邦(国内件）','','FEDEX','',0,1,0,NULL,'',NULL,NULL,NULL,NULL,NULL),
	(21,'FEDEX联邦(国际件）','','FEDEX_GJ','',0,1,0,NULL,'',NULL,NULL,NULL,NULL,NULL);

/*!40000 ALTER TABLE `dux_order_config_express` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_order_config_printer
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_order_config_printer`;

CREATE TABLE `dux_order_config_printer` (
  `printer_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT '',
  `description` varchar(250) DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`printer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `dux_order_config_printer` WRITE;
/*!40000 ALTER TABLE `dux_order_config_printer` DISABLE KEYS */;

INSERT INTO `dux_order_config_printer` (`printer_id`, `name`, `description`, `status`)
VALUES
	(1,'默认','默认打印机',1);

/*!40000 ALTER TABLE `dux_order_config_printer` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_order_config_waybill
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_order_config_waybill`;

CREATE TABLE `dux_order_config_waybill` (
  `config_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `type` varchar(250) DEFAULT '' COMMENT '类型名',
  `setting` text COMMENT '配置内容',
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

LOCK TABLES `dux_order_config_waybill` WRITE;
/*!40000 ALTER TABLE `dux_order_config_waybill` DISABLE KEYS */;

INSERT INTO `dux_order_config_waybill` (`config_id`, `type`, `setting`)
VALUES
	(1,'kdniao','a:4:{s:2:\"id\";s:7:\"1423042\";s:3:\"key\";s:36:\"d8976219-d315-4112-8af5-9353fe345b99\";s:9:\"config_id\";s:1:\"1\";s:4:\"type\";s:6:\"kdniao\";}'),
	(2,'kd100','a:2:{s:9:\"config_id\";s:0:\"\";s:4:\"type\";s:5:\"kd100\";}');

/*!40000 ALTER TABLE `dux_order_config_waybill` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_order_delivery
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_order_delivery`;

CREATE TABLE `dux_order_delivery` (
  `delivery_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) NOT NULL DEFAULT '0' COMMENT '订单ID',
  `delivery_name` varchar(50) DEFAULT '' COMMENT '快递名称',
  `delivery_no` varchar(100) DEFAULT '' COMMENT '快递单号',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `receive_time` int(10) NOT NULL DEFAULT '0' COMMENT '收货时间',
  `receive_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '收货状态',
  `remark` varchar(250) DEFAULT '' COMMENT '发货备注',
  `print_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '打印状态',
  `log` text COMMENT '运单记录',
  `log_update` int(10) NOT NULL DEFAULT '0' COMMENT '记录更新时间',
  `api_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '电子面单',
  `api_data` text COMMENT '电子面单数据',
  PRIMARY KEY (`delivery_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_order_freight
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_order_freight`;

CREATE TABLE `dux_order_freight` (
  `freight_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) DEFAULT '',
  `order_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `exclude_area` text,
  `exclude_ids` text,
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0免费配送 1免费包邮',
  `start_time` int(10) NOT NULL DEFAULT '0',
  `stop_time` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`freight_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_order_gift
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_order_gift`;

CREATE TABLE `dux_order_gift` (
  `gift_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) DEFAULT '',
  `image` varchar(250) DEFAULT '',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0订单 1商品',
  `order_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单金额',
  `mall_ids` text COMMENT '商品ID',
  `has_ids` text COMMENT '关联赠品',
  `start_time` int(10) NOT NULL DEFAULT '0' COMMENT '开始时间',
  `stop_time` int(10) NOT NULL DEFAULT '0' COMMENT '结束时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`gift_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_order_goods
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_order_goods`;

CREATE TABLE `dux_order_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) NOT NULL DEFAULT '0' COMMENT '订单ID',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `has_id` int(10) NOT NULL DEFAULT '0' COMMENT '关联ID',
  `sub_id` int(10) NOT NULL DEFAULT '0' COMMENT '子关联ID',
  `goods_no` varchar(200) DEFAULT '' COMMENT '商品货号',
  `goods_qty` int(10) NOT NULL DEFAULT '1' COMMENT '商品数量',
  `goods_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品单价',
  `goods_cost_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '成本单价',
  `goods_market_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '市场单价',
  `goods_currency` text COMMENT '其他支付',
  `goods_weight` int(10) NOT NULL DEFAULT '0' COMMENT '商品重量',
  `goods_options` text COMMENT '商品属性',
  `goods_name` varchar(250) DEFAULT '' COMMENT '商品名称',
  `goods_image` varchar(250) DEFAULT '' COMMENT '商品图片',
  `goods_url` varchar(250) DEFAULT '' COMMENT '商品链接',
  `goods_point` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品积分',
  `goods_unit` varchar(20) NOT NULL DEFAULT '个',
  `price_total` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品付款价',
  `price_discount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠总价',
  `discount_coupon` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠券折扣',
  `discount_user` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '会员折扣',
  `delivery_id` int(10) NOT NULL DEFAULT '0' COMMENT '快递单ID',
  `delivery_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '物流类型(1需要物流 0无需物流)',
  `delivery_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '发货状态',
  `service_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '售后状态(0未售后 1售后中 2售后完成)',
  `comment_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '评论状态',
  `gift_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '赠品状态',
  `attr_comment` tinyint(1) NOT NULL DEFAULT '1' COMMENT '评论支持',
  `attr_invoice` tinyint(1) NOT NULL DEFAULT '1' COMMENT '发票支持',
  `attr_service` tinyint(1) NOT NULL DEFAULT '1' COMMENT '售后支持',
  `extend` text COMMENT '扩展信息',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `user_id` (`user_id`),
  KEY `has_id` (`has_id`),
  KEY `sub_id` (`sub_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_order_invoice
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_order_invoice`;

CREATE TABLE `dux_order_invoice` (
  `invoice_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `class_id` int(10) NOT NULL DEFAULT '0',
  `order_id` int(10) NOT NULL DEFAULT '0',
  `user_id` int(10) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0个人 1企业',
  `name` varchar(200) DEFAULT '' COMMENT '发票抬头',
  `number` varchar(250) DEFAULT '' COMMENT '纳税人识别号',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '发票金额',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0作废 1待处理 2已开票',
  `create_time` int(10) NOT NULL DEFAULT '0',
  `process_time` int(10) NOT NULL DEFAULT '0',
  `remark` varchar(50) DEFAULT '',
  PRIMARY KEY (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_order_invoice_class
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_order_invoice_class`;

CREATE TABLE `dux_order_invoice_class` (
  `class_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) DEFAULT '',
  `sort` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `dux_order_invoice_class` WRITE;
/*!40000 ALTER TABLE `dux_order_invoice_class` DISABLE KEYS */;

INSERT INTO `dux_order_invoice_class` (`class_id`, `name`, `sort`)
VALUES
	(1,'商品',0);

/*!40000 ALTER TABLE `dux_order_invoice_class` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_order_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_order_log`;

CREATE TABLE `dux_order_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '日志ID',
  `order_id` int(10) NOT NULL DEFAULT '0' COMMENT '订单ID',
  `msg` varchar(250) DEFAULT '' COMMENT '消息',
  `remark` varchar(250) DEFAULT '' COMMENT '备注',
  `time` int(10) NOT NULL DEFAULT '0' COMMENT '时间',
  `ip` varchar(250) DEFAULT '' COMMENT '操作IP',
  `system_id` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`log_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_order_parcel
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_order_parcel`;

CREATE TABLE `dux_order_parcel` (
  `parcel_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) NOT NULL DEFAULT '0' COMMENT '订单ID',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 0 配送失败 1 待配货 2 配货中 3配货完成',
  `remark` varchar(250) DEFAULT '' COMMENT '备注',
  `log` text COMMENT '记录',
  PRIMARY KEY (`parcel_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_order_pay
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_order_pay`;

CREATE TABLE `dux_order_pay` (
  `pay_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0',
  `pay_no` varchar(25) DEFAULT '',
  `order_ids` varchar(250) DEFAULT '',
  `time` int(10) NOT NULL DEFAULT '0',
  `pay_type` varchar(20) DEFAULT 'wechat_miniapp',
  PRIMARY KEY (`pay_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_order_receipt
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_order_receipt`;

CREATE TABLE `dux_order_receipt` (
  `receipt_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) NOT NULL DEFAULT '0' COMMENT '订单ID',
  `order_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单金额',
  `receipt_no` varchar(20) DEFAULT '' COMMENT '收款单号',
  `receipt_pirce` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实收金额',
  `receipt_time` int(10) NOT NULL DEFAULT '0' COMMENT '收款时间',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '收款状态',
  `remark` varchar(250) DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`receipt_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_order_refund
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_order_refund`;

CREATE TABLE `dux_order_refund` (
  `refund_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_goods_id` int(10) NOT NULL DEFAULT '0' COMMENT '订单商品ID',
  `order_id` int(10) NOT NULL DEFAULT '0' COMMENT '订单ID',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `refund_no` varchar(50) DEFAULT '' COMMENT '退款单号',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '退款金额',
  `delivery_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '退款运费',
  `type` int(10) NOT NULL DEFAULT '0' COMMENT '0 退款 1退货 2整单退',
  `cause` varchar(250) DEFAULT '' COMMENT '原因',
  `content` text COMMENT '描述',
  `images` text COMMENT '图片',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态(0取消 1待审核 2退货 3退款)',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `delivery_name` varchar(20) DEFAULT '' COMMENT '快递信息',
  `delivery_no` varchar(50) DEFAULT '' COMMENT '快递单号',
  `process_time` int(10) NOT NULL DEFAULT '0' COMMENT '处理时间',
  `process_remark` varchar(250) DEFAULT '' COMMENT '处理备注',
  PRIMARY KEY (`refund_id`),
  KEY `order_goods_id` (`order_goods_id`),
  KEY `user_id` (`user_id`),
  KEY `refund_no` (`refund_no`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_order_refund_remark
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_order_refund_remark`;

CREATE TABLE `dux_order_refund_remark` (
  `remark_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `refund_id` int(10) NOT NULL DEFAULT '0',
  `user_id` int(10) NOT NULL DEFAULT '0',
  `content` varchar(250) DEFAULT '',
  `time` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`remark_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_order_remark
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_order_remark`;

CREATE TABLE `dux_order_remark` (
  `remark_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) NOT NULL DEFAULT '0',
  `user_id` int(10) NOT NULL DEFAULT '0',
  `content` varchar(250) DEFAULT '',
  `time` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`remark_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_order_take
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_order_take`;

CREATE TABLE `dux_order_take` (
  `take_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '' COMMENT '名称',
  `logo` varchar(250) DEFAULT '' COMMENT '自提点LOGO',
  `tel` varchar(20) DEFAULT '' COMMENT '电话',
  `province` varchar(100) DEFAULT '' COMMENT '省份',
  `city` varchar(100) DEFAULT '' COMMENT '城市',
  `region` varchar(100) DEFAULT '' COMMENT '地区',
  `address` varchar(250) DEFAULT '' COMMENT '详细地址',
  `lat` varchar(50) DEFAULT '' COMMENT '经度',
  `lng` varchar(50) DEFAULT '' COMMENT '纬度',
  `start_time` varchar(20) DEFAULT '' COMMENT '营业开始时间',
  `stop_time` varchar(20) DEFAULT '' COMMENT '营业结束时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `freight` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sort` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`take_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_page
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_page`;

CREATE TABLE `dux_page` (
  `page_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) NOT NULL DEFAULT '0' COMMENT '上级栏目',
  `name` varchar(250) DEFAULT '',
  `image` varchar(250) DEFAULT '',
  `content` longtext,
  `create_time` int(10) NOT NULL DEFAULT '0',
  `update_time` int(10) NOT NULL DEFAULT '0',
  `virtual_view` int(10) NOT NULL DEFAULT '0',
  `view` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort` tinyint(1) NOT NULL DEFAULT '0',
  `keyword` varchar(250) DEFAULT '',
  `description` varchar(250) DEFAULT '',
  `tpl` varchar(250) DEFAULT '',
  PRIMARY KEY (`page_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_pay_account
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_pay_account`;

CREATE TABLE `dux_pay_account` (
  `account_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '资金余额',
  `spend` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '支出金额',
  `charge` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '入账金额',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `recharge` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '充值金额',
  `password` varchar(100) DEFAULT '' COMMENT '支付密码',
  PRIMARY KEY (`account_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_pay_bank
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_pay_bank`;

CREATE TABLE `dux_pay_bank` (
  `bank_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(20) DEFAULT '',
  `name` varchar(250) DEFAULT '' COMMENT '银行名称',
  `logo` varchar(250) DEFAULT '' COMMENT '银行logo',
  `color` varchar(20) DEFAULT '' COMMENT '颜色代码',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`bank_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `dux_pay_bank` WRITE;
/*!40000 ALTER TABLE `dux_pay_bank` DISABLE KEYS */;

INSERT INTO `dux_pay_bank` (`bank_id`, `label`, `name`, `logo`, `color`, `status`)
VALUES
	(1,'CDB','国家开发银行','','c60020',1),
	(2,'ICBC','中国工商银行','','c60020',1),
	(3,'ABC','中国农业银行','','029b82',1),
	(4,'BOC','中国银行','','c60020',1),
	(5,'CCB','中国建设银行','','0050a5',1),
	(6,'PSBC','中国邮政储蓄银行','','029b82',1),
	(7,'COMM','交通银行','','029b82',1),
	(8,'CMB','招商银行','','c60020',1),
	(9,'SPDB','上海浦东发展银行','','0050a5',1),
	(10,'CIB','兴业银行','','0050a5',1),
	(11,'HXBANK','华夏银行','','c60020',1),
	(12,'GDB','广东发展银行','','c60020',1),
	(13,'CMBC','中国民生银行','','029b82',1),
	(14,'CITIC','中信银行','','c60020',1),
	(15,'CEB','中国光大银行','','74008b',1),
	(16,'EGBANK','恒丰银行','','c60020',1),
	(17,'CZBANK','浙商银行','','c60020',1),
	(18,'BOHAIB','渤海银行','','029b82',1),
	(19,'SPABANK','平安银行','','ff0000',1);

/*!40000 ALTER TABLE `dux_pay_bank` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_pay_card
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_pay_card`;

CREATE TABLE `dux_pay_card` (
  `card_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `label` varchar(50) DEFAULT '' COMMENT '银行标识',
  `bank` varchar(20) DEFAULT '' COMMENT '银行名称',
  `bank_color` varchar(20) DEFAULT '' COMMENT '银行颜色',
  `account` varchar(50) DEFAULT '' COMMENT '账户号',
  `account_name` varchar(20) DEFAULT '' COMMENT '账户姓名',
  `type` varchar(20) DEFAULT '' COMMENT '银行卡类型',
  PRIMARY KEY (`card_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_pay_cash
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_pay_cash`;

CREATE TABLE `dux_pay_cash` (
  `cash_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0',
  `cash_no` varchar(50) DEFAULT '' COMMENT '流水号',
  `type` int(1) NOT NULL DEFAULT '0',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `tax` int(10) NOT NULL DEFAULT '0' COMMENT '手续费百分比',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '提现状态',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '提现开始',
  `complete_time` int(10) NOT NULL DEFAULT '0' COMMENT '提现结束',
  `platform` varchar(250) DEFAULT '' COMMENT '提交平台',
  `bank` varchar(50) DEFAULT '' COMMENT '开户行',
  `bank_type` varchar(20) DEFAULT '' COMMENT '账号类型',
  `bank_label` varchar(50) DEFAULT '' COMMENT '银行标识',
  `account` varchar(250) DEFAULT '' COMMENT '提现账户',
  `account_name` varchar(20) DEFAULT '' COMMENT '账户姓名',
  `pay_no` varchar(250) DEFAULT '',
  `pay_name` varchar(250) DEFAULT '',
  `pay_way` varchar(50) DEFAULT '',
  `auth_remark` varchar(250) DEFAULT '' COMMENT '备注',
  `auth_admin` int(10) NOT NULL DEFAULT '0',
  `auth_time` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cash_id`),
  KEY `user_id` (`user_id`),
  KEY `cash_no` (`cash_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_pay_config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_pay_config`;

CREATE TABLE `dux_pay_config` (
  `config_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `type` varchar(250) DEFAULT '' COMMENT '标识',
  `setting` text COMMENT '配置内容',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `sort` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

LOCK TABLES `dux_pay_config` WRITE;
/*!40000 ALTER TABLE `dux_pay_config` DISABLE KEYS */;

INSERT INTO `dux_pay_config` (`config_id`, `type`, `setting`, `status`, `sort`)
VALUES
	(1,'system','a:4:{s:8:\"password\";s:1:\"0\";s:6:\"status\";s:1:\"1\";s:9:\"config_id\";s:1:\"1\";s:4:\"type\";s:6:\"system\";}',1,0);

/*!40000 ALTER TABLE `dux_pay_config` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_pay_recharge
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_pay_recharge`;

CREATE TABLE `dux_pay_recharge` (
  `recharge_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `recharge_no` varchar(50) DEFAULT '' COMMENT '充值单号',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '提交时间',
  `complete_time` int(10) NOT NULL DEFAULT '0' COMMENT '完成时间',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '充值金额',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '充值状态',
  `pay_no` varchar(50) DEFAULT '' COMMENT '交易名',
  `pay_name` varchar(20) DEFAULT '' COMMENT '交易编号',
  `remark` varchar(250) DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`recharge_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_points_account
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_points_account`;

CREATE TABLE `dux_points_account` (
  `account_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `city_id` int(10) NOT NULL DEFAULT '0',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `spend` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '消费积分',
  `charge` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '充值积分',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`account_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_shop_brand
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_shop_brand`;

CREATE TABLE `dux_shop_brand` (
  `brand_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '',
  `home` varchar(250) DEFAULT '',
  `logo` varchar(250) DEFAULT '',
  `sort` int(10) NOT NULL DEFAULT '0',
  `title` varchar(250) DEFAULT '',
  `keyword` varchar(250) DEFAULT '',
  `description` varchar(250) DEFAULT '',
  `status` int(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_shop_faq
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_shop_faq`;

CREATE TABLE `dux_shop_faq` (
  `faq_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `has_id` int(10) NOT NULL DEFAULT '0' COMMENT '商品ID',
  `app` varchar(250) DEFAULT '' COMMENT '应用',
  `time` int(10) NOT NULL DEFAULT '0' COMMENT '资讯时间',
  `content` varchar(250) DEFAULT '' COMMENT '资讯内容',
  `reply_content` varchar(250) DEFAULT '' COMMENT '回复内容',
  `replay_time` int(10) NOT NULL DEFAULT '0' COMMENT '回复时间',
  PRIMARY KEY (`faq_id`),
  KEY `user_id` (`user_id`),
  KEY `has_id` (`has_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_shop_follow
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_shop_follow`;

CREATE TABLE `dux_shop_follow` (
  `follow_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `has_id` int(10) NOT NULL DEFAULT '0' COMMENT '商品ID',
  `app` varchar(50) DEFAULT '' COMMENT '应用',
  `time` int(10) NOT NULL DEFAULT '0' COMMENT '时间',
  `title` varchar(250) DEFAULT '' COMMENT '标题',
  `image` varchar(250) DEFAULT '' COMMENT '形象图',
  `remark` varchar(200) DEFAULT '' COMMENT '备注',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '价格',
  PRIMARY KEY (`follow_id`),
  KEY `user_id` (`user_id`),
  KEY `has_id` (`has_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_shop_footprint
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_shop_footprint`;

CREATE TABLE `dux_shop_footprint` (
  `footprint_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0',
  `ids` text,
  PRIMARY KEY (`footprint_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_shop_spec
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_shop_spec`;

CREATE TABLE `dux_shop_spec` (
  `spec_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '',
  `value` text,
  PRIMARY KEY (`spec_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_shop_spec_group
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_shop_spec_group`;

CREATE TABLE `dux_shop_spec_group` (
  `group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT '',
  `spec_ids` varchar(250) DEFAULT '',
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_site_config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_site_config`;

CREATE TABLE `dux_site_config` (
  `config_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT '',
  `content` varchar(250) DEFAULT '',
  `description` varchar(250) DEFAULT '',
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `dux_site_config` WRITE;
/*!40000 ALTER TABLE `dux_site_config` DISABLE KEYS */;

INSERT INTO `dux_site_config` (`config_id`, `name`, `content`, `description`)
VALUES
	(6,'info_title','DuxSHOP 网上购物商城','站点标题'),
	(7,'info_keyword','DuxSHOP,DuxPHP,PHP,某铺','站点关键词'),
	(8,'info_desc','DuxSHOP是一款免费开源的PHP商城系统','站点描述'),
	(9,'info_copyright','Copyright@2016-2018 www.moupu.com  All Rights Reserved.','版权信息'),
	(10,'info_email','admin@duxphp.com','站点邮箱'),
	(11,'info_tel','','站点电话'),
	(16,'info_name','DuxSHOP','站点名称'),
	(17,'site_status','1','站点状态'),
	(19,'site_error','站定维护中，本次维护预计需要4个小时，请谅解。','关闭说明'),
	(21,'style_primary','#ee1d24',''),
	(22,'style_secondary','#00c5ff',''),
	(23,'style_success','#ee1d24',''),
	(24,'style_warning','#f27b00',''),
	(25,'style_danger','#ef0000',''),
	(26,'style_nav_icon_selected','#ee1d24',''),
	(27,'style_nav_text','#717171',''),
	(28,'style_nav_icon','#c9d6ce',''),
	(29,'style_nav_text_selected','#717171',''),
	(30,'style_member_img','',''),
	(31,'site_wap','https://',''),
	(32,'info_logo','',''),
	(33,'style_login_img','',''),
	(34,'tools_apis','docs','');

/*!40000 ALTER TABLE `dux_site_config` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_site_diy
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_site_diy`;

CREATE TABLE `dux_site_diy` (
  `diy_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT '',
  `fields` text,
  PRIMARY KEY (`diy_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_site_diy_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_site_diy_data`;

CREATE TABLE `dux_site_diy_data` (
  `data_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `diy_id` int(10) NOT NULL DEFAULT '0',
  `title` varchar(250) DEFAULT '',
  `image` varchar(250) DEFAULT '',
  `content` text,
  `editor` tinyint(1) NOT NULL DEFAULT '0',
  `expend` text,
  `sort` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`data_id`) USING BTREE,
  KEY `diy_id` (`diy_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_site_fragment
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_site_fragment`;

CREATE TABLE `dux_site_fragment` (
  `fragment_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(10) DEFAULT '',
  `title` varchar(250) NOT NULL DEFAULT '' COMMENT '描述',
  `content` text NOT NULL COMMENT '内容',
  `editor` tinyint(1) NOT NULL DEFAULT '0' COMMENT '编辑器',
  PRIMARY KEY (`fragment_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_site_position
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_site_position`;

CREATE TABLE `dux_site_position` (
  `pos_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT '',
  `sort` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pos_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_site_search
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_site_search`;

CREATE TABLE `dux_site_search` (
  `search_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `keyword` varchar(20) DEFAULT '',
  `num` int(10) NOT NULL DEFAULT '1',
  `app` varchar(20) DEFAULT '',
  `create_time` int(10) NOT NULL DEFAULT '0',
  `update_time` int(10) NOT NULL DEFAULT '0',
  `has_id` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`search_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_site_tags
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_site_tags`;

CREATE TABLE `dux_site_tags` (
  `tag_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `app` varchar(20) DEFAULT '',
  `name` varchar(250) DEFAULT '',
  `quote` int(10) NOT NULL DEFAULT '1',
  `view` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tag_id`),
  KEY `name` (`name`(191)),
  KEY `quote` (`quote`),
  KEY `view` (`view`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_site_tpl
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_site_tpl`;

CREATE TABLE `dux_site_tpl` (
  `tpl_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT '',
  `label` varchar(50) DEFAULT '',
  `content` longtext,
  `system` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`tpl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `dux_site_tpl` WRITE;
/*!40000 ALTER TABLE `dux_site_tpl` DISABLE KEYS */;

INSERT INTO `dux_site_tpl` (`tpl_id`, `name`, `label`, `content`, `system`)
VALUES
	(1,'首页','index','[{\"style\":{\"limit\":\"10\"},\"key\":\"0\",\"tpl\":\"buytips\",\"data\":[]},{\"data\":[{\"image\":\"http:\\/\\/cdn.duxphp.com\\/static\\/banner\\/banner.png\",\"url\":\"\"}],\"style\":{\"indicatorDots\":\"1\",\"autoplay\":\"1\",\"interval\":\"5000\",\"height\":\"360\"},\"key\":\"1\",\"tpl\":\"swiper\"},{\"style\":{\"height\":\"40\",\"bgColor\":\"#ffffff\"},\"key\":\"2\",\"tpl\":\"empty\",\"data\":[]},{\"style\":{\"class\":\"2\",\"color\":\"#333333\",\"bgColor\":\"#ffffff\",\"limit\":\"4\",\"icon\":\"http:\\/\\/shuichan.shop.moupu.com\\/upload\\/2019-04-30\\/duxup_6d90d33e115dcd61b3a29309d8292bfc.jpg\"},\"key\":\"3\",\"tpl\":\"notice\",\"data\":[]},{\"style\":{\"height\":\"20\",\"bgColor\":\"#ffffff\"},\"key\":\"4\",\"tpl\":\"empty\",\"data\":[]},{\"data\":[{\"image\":\"http:\\/\\/cdn.duxphp.com\\/static\\/icon\\/coupon.png\",\"url\":\"\\/pages\\/coupon\\/index\",\"text\":\"领券中心\"},{\"image\":\"http:\\/\\/cdn.duxphp.com\\/static\\/icon\\/collection.png\",\"url\":\"\\/pages\\/activity\\/list\",\"text\":\"收藏夹\"},{\"image\":\"http:\\/\\/cdn.duxphp.com\\/static\\/icon\\/cart.png\",\"url\":\"\",\"text\":\"购物车\"},{\"image\":\"http:\\/\\/cdn.duxphp.com\\/static\\/icon\\/user.png\",\"url\":\"\",\"text\":\"用户中心\"}],\"style\":{\"column\":\"4\"},\"key\":\"5\",\"tpl\":\"menu\"},{\"style\":{\"height\":\"20\",\"bgColor\":\"#ffffff\"},\"key\":\"6\",\"tpl\":\"empty\",\"data\":[]},{\"style\":{\"attr\":\"0\",\"order\":\"0\",\"class\":\"0\",\"limit\":\"4\"},\"key\":\"7\",\"tpl\":\"goods\",\"data\":[]},{\"data\":{\"content\":\"版权所有 DuxSHOP 商城系统\",\"url\":\"http:\\/\\/www.moupu.com\"},\"style\":{\"fontSize\":\"32\",\"lineHeight\":\"32\",\"textAlign\":\"center\",\"color\":\"#333333\",\"bgColor\":\"#f5f5f5\",\"lrPadding\":\"10\",\"tbPadding\":\"20\"},\"key\":\"8\",\"tpl\":\"text\"}]',1);

/*!40000 ALTER TABLE `dux_site_tpl` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_statis_financial
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_statis_financial`;

CREATE TABLE `dux_statis_financial` (
  `financial_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0',
  `species` varchar(20) CHARACTER SET utf8 DEFAULT '',
  `sub_species` varchar(20) CHARACTER SET utf8 DEFAULT '',
  `date` int(10) NOT NULL DEFAULT '0',
  `charge` decimal(10,2) NOT NULL DEFAULT '0.00',
  `spend` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`financial_id`),
  KEY `species` (`species`),
  KEY `sub_species` (`sub_species`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_statis_financial_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_statis_financial_log`;

CREATE TABLE `dux_statis_financial_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户',
  `log_no` varchar(250) DEFAULT '' COMMENT '流水号',
  `has_no` varchar(100) DEFAULT '' COMMENT '关联单号',
  `has_species` varchar(50) DEFAULT '' COMMENT '关联种类',
  `sub_species` varchar(50) DEFAULT '' COMMENT '关联子类',
  `time` int(10) NOT NULL DEFAULT '0' COMMENT '交易时间',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '交易金额',
  `title` varchar(100) DEFAULT '' COMMENT '交易名称',
  `remark` varchar(250) DEFAULT '' COMMENT '交易备注',
  `pay_no` varchar(200) DEFAULT '' COMMENT '交易号',
  `pay_name` varchar(50) DEFAULT '' COMMENT '交易名',
  `pay_way` varchar(20) DEFAULT '',
  `type` tinyint(1) DEFAULT '0' COMMENT '0支出1收入',
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`),
  KEY `has_no` (`has_no`),
  KEY `has_species` (`has_species`),
  KEY `sub_species` (`sub_species`),
  KEY `time` (`time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_statis_number
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_statis_number`;

CREATE TABLE `dux_statis_number` (
  `num_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `has_id` int(10) NOT NULL DEFAULT '0' COMMENT '关联ID',
  `species` varchar(50) DEFAULT 'mall_sale',
  `date` int(10) NOT NULL DEFAULT '0' COMMENT '日期',
  `inc_num` int(10) NOT NULL DEFAULT '0' COMMENT '增长',
  `dec_num` int(10) NOT NULL DEFAULT '0' COMMENT '减少',
  PRIMARY KEY (`num_id`),
  KEY `user_id` (`user_id`),
  KEY `has_id` (`has_id`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_statis_views
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_statis_views`;

CREATE TABLE `dux_statis_views` (
  `view_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0',
  `date` int(10) NOT NULL DEFAULT '0',
  `has_id` int(10) NOT NULL DEFAULT '0',
  `species` varchar(50) DEFAULT '',
  `type` varchar(20) DEFAULT '',
  `num` int(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`view_id`),
  KEY `date` (`date`),
  KEY `user_id` (`user_id`),
  KEY `has_id` (`has_id`),
  KEY `species` (`species`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `dux_statis_views` WRITE;
/*!40000 ALTER TABLE `dux_statis_views` DISABLE KEYS */;

INSERT INTO `dux_statis_views` (`view_id`, `user_id`, `date`, `has_id`, `species`, `type`, `num`)
VALUES
	(1,0,20190527,0,'site','',40);

/*!40000 ALTER TABLE `dux_statis_views` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_system_debug
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_system_debug`;

CREATE TABLE `dux_system_debug` (
  `debug_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `platform` varchar(50) DEFAULT '',
  `page` varchar(200) DEFAULT '',
  `content` text,
  `hash` varchar(250) DEFAULT '',
  `num` int(10) NOT NULL DEFAULT '1',
  `create_time` int(10) NOT NULL DEFAULT '0',
  `update_time` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`debug_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_system_file
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_system_file`;

CREATE TABLE `dux_system_file` (
  `file_id` int(10) NOT NULL AUTO_INCREMENT,
  `url` varchar(250) DEFAULT '',
  `original` varchar(250) DEFAULT '',
  `title` varchar(250) DEFAULT '',
  `ext` varchar(20) DEFAULT '',
  `size` int(10) NOT NULL DEFAULT '0',
  `time` int(10) NOT NULL DEFAULT '0',
  `user_id` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`file_id`),
  KEY `ext` (`ext`),
  KEY `time` (`time`) USING BTREE,
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='上传文件';



# Dump of table dux_system_role
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_system_role`;

CREATE TABLE `dux_system_role` (
  `role_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT '',
  `description` varchar(250) DEFAULT '',
  `purview` text,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `dux_system_role` WRITE;
/*!40000 ALTER TABLE `dux_system_role` DISABLE KEYS */;

INSERT INTO `dux_system_role` (`role_id`, `name`, `description`, `purview`)
VALUES
	(1,'管理员','系统后台管理员','a:260:{i:0;s:21:\"article.Content.index\";i:1;s:19:\"article.Content.add\";i:2;s:20:\"article.Content.edit\";i:3;s:22:\"article.Content.status\";i:4;s:19:\"article.Content.del\";i:5;s:19:\"article.Class.index\";i:6;s:17:\"article.Class.add\";i:7;s:18:\"article.Class.edit\";i:8;s:20:\"article.Class.status\";i:9;s:17:\"article.Class.del\";i:10;s:18:\"mall.Content.index\";i:11;s:16:\"mall.Content.add\";i:12;s:17:\"mall.Content.edit\";i:13;s:19:\"mall.Content.status\";i:14;s:16:\"mall.Content.del\";i:15;s:16:\"mall.Class.index\";i:16;s:14:\"mall.Class.add\";i:17;s:15:\"mall.Class.edit\";i:18;s:17:\"mall.Class.status\";i:19;s:14:\"mall.Class.del\";i:20;s:16:\"mall.Order.index\";i:21;s:15:\"mall.Order.info\";i:22;s:18:\"mall.Comment.index\";i:23;s:19:\"mall.Comment.status\";i:24;s:22:\"mall.SellRanking.index\";i:25;s:19:\"mall.SellList.index\";i:26;s:22:\"marketing.Coupon.index\";i:27;s:20:\"marketing.Coupon.add\";i:28;s:21:\"marketing.Coupon.edit\";i:29;s:23:\"marketing.Coupon.status\";i:30;s:20:\"marketing.Coupon.del\";i:31;s:27:\"marketing.CouponClass.index\";i:32;s:25:\"marketing.CouponClass.add\";i:33;s:26:\"marketing.CouponClass.edit\";i:34;s:28:\"marketing.CouponClass.status\";i:35;s:25:\"marketing.CouponClass.del\";i:36;s:25:\"marketing.CouponLog.index\";i:37;s:23:\"marketing.CouponLog.del\";i:38;s:26:\"marketing.CouponGift.index\";i:39;s:24:\"marketing.CouponGift.add\";i:40;s:25:\"marketing.CouponGift.edit\";i:41;s:27:\"marketing.CouponGift.status\";i:42;s:24:\"marketing.CouponGift.del\";i:43;s:25:\"marketing.CouponRec.index\";i:44;s:23:\"marketing.CouponRec.add\";i:45;s:24:\"marketing.CouponRec.edit\";i:46;s:26:\"marketing.CouponRec.status\";i:47;s:23:\"marketing.CouponRec.del\";i:48;s:23:\"member.MemberUser.index\";i:49;s:21:\"member.MemberUser.add\";i:50;s:22:\"member.MemberUser.edit\";i:51;s:24:\"member.MemberUser.status\";i:52;s:21:\"member.MemberUser.del\";i:53;s:24:\"member.MemberGrade.index\";i:54;s:22:\"member.MemberGrade.add\";i:55;s:23:\"member.MemberGrade.edit\";i:56;s:25:\"member.MemberGrade.status\";i:57;s:22:\"member.MemberGrade.del\";i:58;s:23:\"member.MemberReal.index\";i:59;s:23:\"member.MemberReal.check\";i:60;s:23:\"member.MemberRole.index\";i:61;s:21:\"member.MemberRole.add\";i:62;s:22:\"member.MemberRole.edit\";i:63;s:21:\"member.MemberRole.del\";i:64;s:23:\"member.PayAccount.index\";i:65;s:19:\"member.PayLog.index\";i:66;s:20:\"member.PayCash.index\";i:67;s:20:\"member.PayCard.index\";i:68;s:18:\"member.PayCard.add\";i:69;s:19:\"member.PayCard.edit\";i:70;s:18:\"member.PayCard.del\";i:71;s:20:\"member.PayConf.index\";i:72;s:22:\"member.PayConf.setting\";i:73;s:20:\"member.PayBank.index\";i:74;s:18:\"member.PayBank.add\";i:75;s:19:\"member.PayBank.edit\";i:76;s:18:\"member.PayBank.del\";i:77;s:26:\"member.PointsAccount.index\";i:78;s:22:\"member.PointsLog.index\";i:79;s:25:\"member.MemberConfig.index\";i:80;s:23:\"member.MemberConfig.reg\";i:81;s:25:\"member.MemberVerify.index\";i:82;s:26:\"member.MemberVerify.status\";i:83;s:23:\"member.MemberVerify.del\";i:84;s:26:\"member.MemberRanking.index\";i:85;s:24:\"member.MemberTrend.index\";i:86;s:18:\"order.Config.index\";i:87;s:25:\"order.ConfigExpress.index\";i:88;s:25:\"order.ConfigPrinter.index\";i:89;s:23:\"order.ConfigPrinter.add\";i:90;s:24:\"order.ConfigPrinter.edit\";i:91;s:26:\"order.ConfigPrinter.status\";i:92;s:23:\"order.ConfigPrinter.del\";i:93;s:26:\"order.ConfigDelivery.index\";i:94;s:25:\"order.ConfigWaybill.index\";i:95;s:27:\"order.ConfigWaybill.setting\";i:96;s:18:\"order.Parcel.index\";i:97;s:18:\"order.Parcel.print\";i:98;s:19:\"order.Parcel.status\";i:99;s:16:\"order.Parcel.del\";i:100;s:20:\"order.Delivery.index\";i:101;s:20:\"order.Delivery.print\";i:102;s:21:\"order.Delivery.status\";i:103;s:18:\"order.Delivery.del\";i:104;s:19:\"order.Receipt.index\";i:105;s:20:\"order.Receipt.status\";i:106;s:17:\"order.Receipt.del\";i:107;s:19:\"order.Comment.index\";i:108;s:18:\"order.Refund.index\";i:109;s:17:\"order.Refund.info\";i:110;s:16:\"order.Take.index\";i:111;s:14:\"order.Take.add\";i:112;s:15:\"order.Take.edit\";i:113;s:17:\"order.Take.status\";i:114;s:14:\"order.Take.del\";i:115;s:19:\"order.Invoice.index\";i:116;s:20:\"order.Invoice.status\";i:117;s:17:\"order.Invoice.del\";i:118;s:24:\"order.InvoiceClass.index\";i:119;s:22:\"order.InvoiceClass.add\";i:120;s:23:\"order.InvoiceClass.edit\";i:121;s:25:\"order.InvoiceClass.status\";i:122;s:22:\"order.InvoiceClass.del\";i:123;s:23:\"order.OrderStatis.index\";i:124;s:16:\"shop.Brand.index\";i:125;s:14:\"shop.Brand.add\";i:126;s:15:\"shop.Brand.edit\";i:127;s:17:\"shop.Brand.status\";i:128;s:14:\"shop.Brand.del\";i:129;s:21:\"shop.BrandApply.index\";i:130;s:19:\"shop.BrandApply.add\";i:131;s:20:\"shop.BrandApply.edit\";i:132;s:22:\"shop.BrandApply.status\";i:133;s:19:\"shop.BrandApply.del\";i:134;s:24:\"shop.BrandContract.index\";i:135;s:22:\"shop.BrandContract.add\";i:136;s:23:\"shop.BrandContract.edit\";i:137;s:25:\"shop.BrandContract.status\";i:138;s:22:\"shop.BrandContract.del\";i:139;s:15:\"shop.Spec.index\";i:140;s:13:\"shop.Spec.add\";i:141;s:14:\"shop.Spec.edit\";i:142;s:16:\"shop.Spec.status\";i:143;s:13:\"shop.Spec.del\";i:144;s:20:\"shop.SpecGroup.index\";i:145;s:18:\"shop.SpecGroup.add\";i:146;s:19:\"shop.SpecGroup.edit\";i:147;s:21:\"shop.SpecGroup.status\";i:148;s:18:\"shop.SpecGroup.del\";i:149;s:17:\"shop.Config.index\";i:150;s:17:\"site.Config.index\";i:151;s:15:\"site.Config.tpl\";i:152;s:17:\"site.Search.index\";i:153;s:15:\"site.Search.add\";i:154;s:16:\"site.Search.edit\";i:155;s:15:\"site.Search.del\";i:156;s:19:\"site.Fragment.index\";i:157;s:17:\"site.Fragment.add\";i:158;s:18:\"site.Fragment.edit\";i:159;s:20:\"site.Fragment.status\";i:160;s:17:\"site.Fragment.del\";i:161;s:14:\"site.Diy.index\";i:162;s:12:\"site.Diy.add\";i:163;s:13:\"site.Diy.edit\";i:164;s:15:\"site.Diy.status\";i:165;s:12:\"site.Diy.del\";i:166;s:18:\"site.DiyData.index\";i:167;s:16:\"site.DiyData.add\";i:168;s:17:\"site.DiyData.edit\";i:169;s:19:\"site.DiyData.status\";i:170;s:16:\"site.DiyData.del\";i:171;s:14:\"site.Tpl.index\";i:172;s:12:\"site.Tpl.add\";i:173;s:13:\"site.Tpl.edit\";i:174;s:12:\"site.Tpl.del\";i:175;s:22:\"statis.SiteViews.index\";i:176;s:18:\"system.Index.index\";i:177;s:21:\"system.Index.userData\";i:178;s:19:\"system.Notice.index\";i:179;s:17:\"system.Notice.del\";i:180;s:19:\"system.Update.index\";i:181;s:19:\"system.Config.index\";i:182;s:18:\"system.Config.user\";i:183;s:18:\"system.Config.info\";i:184;s:20:\"system.Config.upload\";i:185;s:25:\"system.ConfigManage.index\";i:186;s:23:\"system.ConfigManage.add\";i:187;s:24:\"system.ConfigManage.edit\";i:188;s:26:\"system.ConfigManage.status\";i:189;s:23:\"system.ConfigManage.del\";i:190;s:22:\"system.ConfigApi.index\";i:191;s:20:\"system.ConfigApi.add\";i:192;s:21:\"system.ConfigApi.edit\";i:193;s:23:\"system.ConfigApi.status\";i:194;s:20:\"system.ConfigApi.del\";i:195;s:25:\"system.ConfigUpload.index\";i:196;s:24:\"system.ConfigUpload.edit\";i:197;s:17:\"system.User.index\";i:198;s:15:\"system.User.add\";i:199;s:16:\"system.User.edit\";i:200;s:18:\"system.User.status\";i:201;s:15:\"system.User.del\";i:202;s:17:\"system.Role.index\";i:203;s:15:\"system.Role.add\";i:204;s:16:\"system.Role.edit\";i:205;s:15:\"system.Role.del\";i:206;s:18:\"system.Debug.index\";i:207;s:16:\"system.Debug.del\";i:208;s:22:\"system.SystemLog.index\";i:209;s:20:\"system.SystemLog.del\";i:210;s:24:\"system.Application.index\";i:211;s:22:\"system.Application.add\";i:212;s:23:\"system.Application.edit\";i:213;s:22:\"system.Application.del\";i:214;s:20:\"tools.SendData.index\";i:215;s:16:\"tools.Send.index\";i:216;s:14:\"tools.Send.add\";i:217;s:15:\"tools.Send.info\";i:218;s:20:\"tools.SendConf.index\";i:219;s:22:\"tools.SendConf.setting\";i:220;s:19:\"tools.SendTpl.index\";i:221;s:17:\"tools.SendTpl.add\";i:222;s:18:\"tools.SendTpl.edit\";i:223;s:17:\"tools.SendTpl.del\";i:224;s:23:\"tools.SendDefault.index\";i:225;s:17:\"tools.Label.index\";i:226;s:17:\"tools.Queue.index\";i:227;s:21:\"tools.QueueConf.index\";i:228;s:15:\"tools.Api.index\";i:229;s:14:\"tools.Api.make\";i:230;s:21:\"warehouse.Marki.index\";i:231;s:19:\"warehouse.Marki.add\";i:232;s:20:\"warehouse.Marki.edit\";i:233;s:19:\"warehouse.Marki.del\";i:234;s:29:\"warehouse.MarkiDelivery.index\";i:235;s:28:\"warehouse.MarkiWarning.index\";i:236;s:31:\"warehouse.MarkiWarningLog.index\";i:237;s:24:\"warehouse.Supplier.index\";i:238;s:22:\"warehouse.Supplier.add\";i:239;s:23:\"warehouse.Supplier.edit\";i:240;s:22:\"warehouse.Supplier.del\";i:241;s:29:\"warehouse.SupplierOrder.index\";i:242;s:22:\"warehouse.PosLog.index\";i:243;s:20:\"warehouse.PosLog.del\";i:244;s:25:\"warehouse.ConfigPos.index\";i:245;s:23:\"warehouse.ConfigPos.add\";i:246;s:24:\"warehouse.ConfigPos.edit\";i:247;s:23:\"warehouse.ConfigPos.del\";i:248;s:25:\"warehouse.PosDriver.index\";i:249;s:23:\"warehouse.PosDriver.add\";i:250;s:24:\"warehouse.PosDriver.edit\";i:251;s:23:\"warehouse.PosDriver.del\";i:252;s:22:\"warehouse.PosTpl.index\";i:253;s:20:\"warehouse.PosTpl.add\";i:254;s:21:\"warehouse.PosTpl.edit\";i:255;s:20:\"warehouse.PosTpl.del\";i:256;s:25:\"wechat.WechatConfig.index\";i:257;s:23:\"wechat.MenuConfig.index\";i:258;s:26:\"wechat.MiniappConfig.index\";i:259;s:22:\"wechat.AppConfig.index\";}');

/*!40000 ALTER TABLE `dux_system_role` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_system_statistics
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_system_statistics`;

CREATE TABLE `dux_system_statistics` (
  `stat_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` varchar(8) DEFAULT '',
  `web` int(10) NOT NULL DEFAULT '0',
  `api` int(10) NOT NULL DEFAULT '0',
  `mobile` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_system_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_system_user`;

CREATE TABLE `dux_system_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) NOT NULL DEFAULT '0',
  `nickname` varchar(20) DEFAULT '',
  `username` varchar(20) DEFAULT '',
  `password` varchar(128) DEFAULT '',
  `avatar` varchar(250) DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `reg_time` int(10) NOT NULL DEFAULT '0',
  `login_time` int(10) NOT NULL DEFAULT '0',
  `login_ip` varchar(50) DEFAULT '',
  `role_ext` varchar(250) DEFAULT '',
  PRIMARY KEY (`user_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `dux_system_user` WRITE;
/*!40000 ALTER TABLE `dux_system_user` DISABLE KEYS */;

INSERT INTO `dux_system_user` (`user_id`, `role_id`, `nickname`, `username`, `password`, `avatar`, `status`, `reg_time`, `login_time`, `login_ip`, `role_ext`)
VALUES
	(1,1,'admin','admin','e10adc3949ba59abbe56e057f20f883e','',1,0,0,'127.0.0.1','');

/*!40000 ALTER TABLE `dux_system_user` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_tools_queue
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_tools_queue`;

CREATE TABLE `dux_tools_queue` (
  `queue_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(20) DEFAULT '' COMMENT '关联标记',
  `has_id` int(10) NOT NULL DEFAULT '0' COMMENT '关联ID',
  `target` varchar(250) DEFAULT '' COMMENT '模块',
  `action` varchar(20) DEFAULT '' COMMENT '方法名',
  `layer` varchar(20) DEFAULT '' COMMENT '层',
  `params` text COMMENT '参数',
  `remark` varchar(250) DEFAULT '' COMMENT '备注',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `start_time` int(10) NOT NULL DEFAULT '0' COMMENT '开始时间',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `run_time` int(10) NOT NULL DEFAULT '0' COMMENT '执行时间',
  `run_num` int(3) NOT NULL DEFAULT '0' COMMENT '运行次数',
  `max_num` int(2) NOT NULL DEFAULT '0' COMMENT '最大次数',
  `message` text COMMENT '返回消息',
  PRIMARY KEY (`queue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_tools_queue_config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_tools_queue_config`;

CREATE TABLE `dux_tools_queue_config` (
  `config_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT '' COMMENT '类型名',
  `content` text COMMENT '配置内容',
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `dux_tools_queue_config` WRITE;
/*!40000 ALTER TABLE `dux_tools_queue_config` DISABLE KEYS */;

INSERT INTO `dux_tools_queue_config` (`config_id`, `name`, `content`)
VALUES
	(1,'lock_time','60'),
	(2,'every_num','5'),
	(3,'retry_num','5'),
	(4,'del_status','1'),
	(5,'status','1');

/*!40000 ALTER TABLE `dux_tools_queue_config` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_tools_send
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_tools_send`;

CREATE TABLE `dux_tools_send` (
  `send_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `receive` varchar(250) DEFAULT '' COMMENT '接收账号',
  `title` varchar(250) DEFAULT '' COMMENT '发送标题',
  `content` text COMMENT '发送内容',
  `param` text COMMENT '附加参数',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '发送状态',
  `type` varchar(50) DEFAULT '' COMMENT '发送类型',
  `start_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `stop_time` int(10) NOT NULL DEFAULT '0' COMMENT '结束时间',
  `remark` varchar(250) DEFAULT '' COMMENT '备注',
  `user_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否会员',
  PRIMARY KEY (`send_id`),
  KEY `type` (`type`),
  KEY `start_time` (`start_time`),
  KEY `stop_time` (`stop_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;



# Dump of table dux_tools_send_config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_tools_send_config`;

CREATE TABLE `dux_tools_send_config` (
  `config_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `type` varchar(250) DEFAULT '' COMMENT '类型名',
  `setting` text COMMENT '配置内容',
  PRIMARY KEY (`config_id`),
  KEY `type` (`type`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;



# Dump of table dux_tools_send_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_tools_send_data`;

CREATE TABLE `dux_tools_send_data` (
  `data_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(50) DEFAULT '',
  `label` varchar(250) DEFAULT '',
  `class` varchar(250) DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(250) DEFAULT '',
  `data` text,
  `url` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`data_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_tools_send_default
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_tools_send_default`;

CREATE TABLE `dux_tools_send_default` (
  `default_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `class` varchar(50) DEFAULT '' COMMENT '种类',
  `type` varchar(50) DEFAULT '' COMMENT '类型',
  `tpl` text COMMENT '基础模板',
  PRIMARY KEY (`default_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `dux_tools_send_default` WRITE;
/*!40000 ALTER TABLE `dux_tools_send_default` DISABLE KEYS */;

INSERT INTO `dux_tools_send_default` (`default_id`, `class`, `type`, `tpl`)
VALUES
	(1,'sms','alsms',''),
	(2,'mail','email','<figure class=\"table\">\r\n<table style=\"height: 167px;\" width=\"467\">\r\n<tbody>\r\n<tr>\r\n<td style=\"width: 452.219px;\">\r\n<p>[内容区域]</p>\r\n<p>此为系统邮件，请勿回复<br />请保管好您的邮箱，避免账号被他人盗用2</p>\r\n<p>[网站名称] [网址]</p>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</figure>\r\n<p><br />&nbsp;</p>'),
	(3,'wechat','wechat',''),
	(4,'app','xiaomi',''),
	(5,'mail_tpl','<figure class=\"table\">\r\n<table style=\"height: 167p',''),
	(6,'site','site','');

/*!40000 ALTER TABLE `dux_tools_send_default` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_tools_send_tpl
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_tools_send_tpl`;

CREATE TABLE `dux_tools_send_tpl` (
  `tpl_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(250) DEFAULT '' COMMENT '模板标题',
  `content` text COMMENT '模板内容',
  `time` int(10) NOT NULL DEFAULT '0' COMMENT '时间',
  PRIMARY KEY (`tpl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_warehouse_config_pos
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_warehouse_config_pos`;

CREATE TABLE `dux_warehouse_config_pos` (
  `pos_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `type` varchar(250) DEFAULT '' COMMENT '类型名',
  `setting` text COMMENT '配置内容',
  PRIMARY KEY (`pos_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

LOCK TABLES `dux_warehouse_config_pos` WRITE;
/*!40000 ALTER TABLE `dux_warehouse_config_pos` DISABLE KEYS */;

INSERT INTO `dux_warehouse_config_pos` (`pos_id`, `type`, `setting`)
VALUES
	(1,'yilianyun','');

/*!40000 ALTER TABLE `dux_warehouse_config_pos` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_warehouse_marki
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_warehouse_marki`;

CREATE TABLE `dux_warehouse_marki` (
  `marki_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `user_id` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`marki_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_warehouse_marki_delivery
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_warehouse_marki_delivery`;

CREATE TABLE `dux_warehouse_marki_delivery` (
  `delivery_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `marki_id` int(10) NOT NULL DEFAULT '0',
  `order_id` int(10) NOT NULL DEFAULT '0',
  `goods_ids` varchar(250) DEFAULT '',
  `create_time` int(10) NOT NULL DEFAULT '0',
  `receive_time` int(10) NOT NULL DEFAULT '0',
  `receive_status` tinyint(1) NOT NULL DEFAULT '0',
  `remark` varchar(250) DEFAULT '',
  PRIMARY KEY (`delivery_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_warehouse_pos_driver
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_warehouse_pos_driver`;

CREATE TABLE `dux_warehouse_pos_driver` (
  `driver_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_id` int(10) NOT NULL DEFAULT '0',
  `name` varchar(100) DEFAULT '',
  `description` varchar(250) DEFAULT '',
  `number` varchar(250) DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`driver_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `dux_warehouse_pos_driver` WRITE;
/*!40000 ALTER TABLE `dux_warehouse_pos_driver` DISABLE KEYS */;

INSERT INTO `dux_warehouse_pos_driver` (`driver_id`, `pos_id`, `name`, `description`, `number`, `status`)
VALUES
	(1,1,'默认打印机','默认','0',1);

/*!40000 ALTER TABLE `dux_warehouse_pos_driver` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_warehouse_pos_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_warehouse_pos_log`;

CREATE TABLE `dux_warehouse_pos_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `driver_id` int(10) NOT NULL DEFAULT '0',
  `pos_no` varchar(250) DEFAULT '',
  `time` int(10) NOT NULL DEFAULT '0',
  `content` text,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_warehouse_pos_tpl
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_warehouse_pos_tpl`;

CREATE TABLE `dux_warehouse_pos_tpl` (
  `tpl_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pos_id` int(10) NOT NULL DEFAULT '0',
  `name` varchar(100) DEFAULT '',
  `tpl` text,
  PRIMARY KEY (`tpl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `dux_warehouse_pos_tpl` WRITE;
/*!40000 ALTER TABLE `dux_warehouse_pos_tpl` DISABLE KEYS */;

INSERT INTO `dux_warehouse_pos_tpl` (`tpl_id`, `pos_id`, `name`, `tpl`)
VALUES
	(1,1,'配送订单','<FH><FB><center>DuxShop</center></FB></FH>\r\n********************************\\r\r\n<FH>\r\n姓名：{$orderInfo.receive_name}\\r\r\n电话:   {$orderInfo.receive_tel}\\r\r\n地址：{$orderInfo.receive_school} {$orderInfo.receive_floor} {$orderInfo.receive_address}\\r\r\n日期：{date(\'Y-m-d H:i:s\', $orderInfo.order_create_time)}\\r\r\n单号：{$orderInfo.order_no}</FH>\\r\r\n********************************\\r<FH>\r\n\r\n名称 单价 数量 付款\r\n<!--loop{$goodsData as $vo}-->\r\n{$vo.name}  {$vo.option}\r\n        {$vo.price} {$vo.num} {$vo.total}\r\n<!--{/loop}-->\r\n买家备注：\r\n{$orderInfo.remark}\r\n\\r\r\n</FH>\r\n********************************\\r\r\n<FH>\r\n<right>总价：{$totalPrice}元</right>\r\n</FH>\r\n********************************\\r\r\n<QR>{$deliveryNo}</QR>');

/*!40000 ALTER TABLE `dux_warehouse_pos_tpl` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_warehouse_supplier
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_warehouse_supplier`;

CREATE TABLE `dux_warehouse_supplier` (
  `supplier_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT '',
  `tel` varchar(20) DEFAULT '',
  `status` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_warehouse_supplier_order
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_warehouse_supplier_order`;

CREATE TABLE `dux_warehouse_supplier_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int(10) DEFAULT '0',
  `order_goods_id` int(10) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table dux_warehouse_warning
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_warehouse_warning`;

CREATE TABLE `dux_warehouse_warning` (
  `warning_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0',
  `start_time` int(10) NOT NULL DEFAULT '0',
  `stop_time` int(10) NOT NULL DEFAULT '0',
  `remark` text,
  PRIMARY KEY (`warning_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table dux_wechat_app
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_wechat_app`;

CREATE TABLE `dux_wechat_app` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT '',
  `label` varchar(20) DEFAULT '',
  `appid` varchar(100) NOT NULL DEFAULT '',
  `secret` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `dux_wechat_app` WRITE;
/*!40000 ALTER TABLE `dux_wechat_app` DISABLE KEYS */;

INSERT INTO `dux_wechat_app` (`id`, `name`, `label`, `appid`, `secret`)
VALUES
	(1,'商城APP','main','','');

/*!40000 ALTER TABLE `dux_wechat_app` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_wechat_config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_wechat_config`;

CREATE TABLE `dux_wechat_config` (
  `config_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT '',
  `content` text,
  `description` varchar(250) DEFAULT '',
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `dux_wechat_config` WRITE;
/*!40000 ALTER TABLE `dux_wechat_config` DISABLE KEYS */;

INSERT INTO `dux_wechat_config` (`config_id`, `name`, `content`, `description`)
VALUES
	(1,'appid','','AppID'),
	(2,'secret','','AppSecret'),
	(3,'token','','Token'),
	(4,'aeskey','','EncodingAESKey'),
	(7,'message_focus','欢迎关注某铺',''),
	(8,'message_name','某铺',''),
	(10,'mp_name','某铺',''),
	(11,'mp_desc','点击保存到相册可以识别二维码,复制公众号可以到微信进行搜索',''),
	(12,'mp_qrcode','public/images/qrcode.jpg',''),
	(13,'mp_focus','0','');

/*!40000 ALTER TABLE `dux_wechat_config` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_wechat_menu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_wechat_menu`;

CREATE TABLE `dux_wechat_menu` (
  `menu_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) NOT NULL DEFAULT '0',
  `name` varchar(100) DEFAULT '',
  `type` tinyint(1) NOT NULL,
  `sort` int(10) NOT NULL DEFAULT '0',
  `data` text,
  PRIMARY KEY (`menu_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `dux_wechat_menu` WRITE;
/*!40000 ALTER TABLE `dux_wechat_menu` DISABLE KEYS */;

INSERT INTO `dux_wechat_menu` (`menu_id`, `parent_id`, `name`, `type`, `sort`, `data`)
VALUES
	(13,0,'默认菜单',2,0,'{\"type\":\"view\",\"url\":\"http:\\/\\/www.duxphp.com\"}');

/*!40000 ALTER TABLE `dux_wechat_menu` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table dux_wechat_miniapp
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dux_wechat_miniapp`;

CREATE TABLE `dux_wechat_miniapp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT '',
  `label` varchar(20) DEFAULT '',
  `appid` varchar(100) NOT NULL DEFAULT '',
  `secret` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `dux_wechat_miniapp` WRITE;
/*!40000 ALTER TABLE `dux_wechat_miniapp` DISABLE KEYS */;

INSERT INTO `dux_wechat_miniapp` (`id`, `name`, `label`, `appid`, `secret`)
VALUES
	(1,'主程序','main','','');

/*!40000 ALTER TABLE `dux_wechat_miniapp` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
