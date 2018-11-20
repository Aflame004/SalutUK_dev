create database human_resource;
use human_resource;
create table user(
	user_id int primary key AUTO_INCREMENT,
	wechat_open_id varchar(40),
	wechat_union_id varchar(40),
	wechat_name varchar(255),
	fb_id varchar(25),
	fb_name varchar(255),
	email varchar(255) unique,
	password varchar(40),
	is_activated tinyint(1) not null default 0 comment '0代表未激活，1代表激活，2代表冻结',
	regist_time datetime,
	last_login_time datetime,
	token varchar(32),
	role int default 0 comment '0代表待定,1代表求职者，2代表雇主，3代表系统管理员',
	admin_group int default 0 comment '0代表没有管理组'
);
/*
	password初始为123456
	password是md5(password+salt)的结果,其中salt=*$uU4P#2lxx*xalsifoasdvx8alNM~
	即md5(123456*$uU4P#2lxx*xalsifoasdvx8alNM~)的运算结果
*/
insert into user(email,password,is_activated,regist_time,role,admin_group) values(
	'admin@admin.com',
	'4f3ea32c01f7a5a7d80e92b39d2cf36f',
	1,
	now(),
	3,
	1
);

create table user_info(
	user_id int primary key,
	user_name varchar(100) not null,
	id_photo varchar (100) not null,
	phone varchar(20) not null,
	age int,
	profile_info text(500),
	work_history text(500),
	verification_status tinyint default 0 comment '0待审核 1审核通过 2审核失败',
	fail_reason varchar(100) comment "审核失败原因"
);

create table job_hunting
(
	id int primary key AUTO_INCREMENT,
	user_id int not null,
	first_name varchar(30),
	surname varchar(30),
	specify varchar(255) comment '应征岗位',
	specify2 varchar(255),
	work_start_date date,
	city_want varchar(255) comment '预期工作城市',
	right_uk varchar(10) comment '能否在英国合法工作',
	gender varchar(20),
	height varchar(5),
	weight varchar(5),
	birthday date,
	marital_status varchar(20) comment '婚姻情况',
	street varchar(255),
	street2 varchar(255),
	city varchar(100),
	`state` varchar(100),
	postal varchar(10),
	mobile_district varchar(10) comment '手机区号',
	mobile varchar(15) comment '手机号',
	email varchar(255),
	insurance_num varchar(200) comment '国民保险号码',
	emergency_first_name varchar(30),
	emergency_last_name varchar(30),
	emergency_mobile varchar(30),
	emergency_relationship varchar(30),
	education varchar(50),
	`language` varchar(255),
	fluency_english varchar(50),
	driver_licences varchar(10),
	employment_history varchar(10),
	work_skills varchar(255),
	salary varchar(20),
	criminal varchar(10),
	criminal_detail varchar(255),
	health varchar(10),
	health_detail varchar(255),
	where_know varchar(50),
	upload_cv varchar(255),
	id_photo varchar(255),
	apply_time datetime,
	status tinyint comment '1开启,2匹配中,3关闭'
);

create table post_class
(
	post_id int primary key,
	post_name varchar(255),
	describes varchar(255) comment '职位描述',
	is_del tinyint default 0 comment '0存在 1被删除'
);

create table job_publishment
(
	id int primary key AUTO_INCREMENT,
	user_id int not null,
	name varchar(200),
	business_name varchar(200),
	address varchar(200),
	city varchar(200),
	country varchar(200),
	postcode varchar(10),
	landline varchar(20),
	accomodation varchar(10) comment '是否提供宿舍',
	distance varchar(10) comment '宿舍到公司距离',
	company_name varchar(200),
	vat_num varchar(50),
	job_vacancy varchar(50),
	job_specification1 varchar(50),
	job_specification2 varchar(50),
	job_specification3 varchar(50),
	skill_reqired varchar(100),
	start_date date,
	special_requirement varchar(100),
	contact_person varchar(50),
	mobile varchar(15),
	wechat varchar(50),
	email varchar(255),
	where_know varchar(50),
	address_upload varchar(255),
	apply_time datetime,
	status tinyint comment '1开启,2匹配中,3关闭'
);

create table job_match
(
	id int primary key AUTO_INCREMENT,
	job_hunting_id int,
	job_publishment_id int,
	status tinyint comment '1已提交匹配2匹配完成3匹配关闭',
	match_time datetime
);

create table auth_item
(
	auth_id int primary key,
	item_name varchar(20) comment '菜单描述'
);

create table admin_group
(
	group_id int,
	auth_id int not null
);

create table admin_group_describe
(
	group_id int primary key AUTO_INCREMENT,
	group_name varchar(100),
	status tinyint default 1 comment '0为冻结，1为激活'
);

create table admin_info
(
	user_id int primary key,
	name varchar(50),
	mobile varchar(20)
);
insert into auth_item values(1,'账户管理');
insert into auth_item values(2,'修改用户余额');
insert into auth_item values(3,'查看用户信息');
insert into auth_item values(4,'匹配用户');
insert into auth_item values(5,'新闻编辑');
insert into auth_item values(6,'管理员账号管理');
insert into auth_item values(7,'审核信息');

insert into admin_group_describe values(1,'超级用户组',1);
insert into admin_group values(1,1);
insert into admin_group values(1,2);
insert into admin_group values(1,3);
insert into admin_group values(1,4);
insert into admin_group values(1,5);
insert into admin_group values(1,6);
insert into admin_group values(1,7);


create table company_info
(
	company_id int primary key AUTO_INCREMENT,
	user_id int,
	company_name varchar(255),
	business text(500) comment '公司主营业务',
	introduction text(500) comment '公司概况',
	website varchar(255) comment '公司网站',
	apply_time datetime comment '申请日期 ',
	status tinyint comment '0待审核，1审核通过,2审核失败',
	fail_reason varchar(255) comment '审核失败原因 '
);


insert into admin_info values (1,'超级管理','');

-- ----------------------------
--  Table structure for `cate`
-- ----------------------------
DROP TABLE IF EXISTS `cate`;
CREATE TABLE `cate` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT COMMENT '栏目id',
  `catename` varchar(30) NOT NULL COMMENT '栏目名称',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Records of `cate`
-- ----------------------------
BEGIN;
INSERT INTO `cate` VALUES ('1', '新闻');
COMMIT;

-- ----------------------------
--  Table structure for `article`
-- ----------------------------
DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT COMMENT '文章id',
  `title` varchar(60) NOT NULL COMMENT '文章标题',
  `author` varchar(30) NOT NULL COMMENT '文章作者',
  `desc` varchar(255) NOT NULL COMMENT '文章简介',
  `keywords` varchar(255) NOT NULL COMMENT '文章关键词',
  `content` text NOT NULL COMMENT '文章内容',
  `pic` varchar(100) NOT NULL COMMENT '缩略图',
  `click` int(10) NOT NULL DEFAULT '0' COMMENT '点击数',
  `state` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0：不推荐 1：推荐',
  `time` int(10) NOT NULL COMMENT '发布时间',
  `cateid` mediumint(9) NOT NULL COMMENT '所属栏目',
  `photo` mediumtext COMMENT '图册',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;