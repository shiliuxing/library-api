-- phpMyAdmin SQL Dump
-- version 4.8.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 2018-05-26 07:09:02
-- 服务器版本： 5.7.15
-- PHP Version: 7.1.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: library
--

-- --------------------------------------------------------

--
-- 表的结构 booklists
--

CREATE TABLE booklists (
  id int(11) NOT NULL,
  wechat_user_id int(11) NOT NULL,
  title varchar(100) NOT NULL COMMENT '书单标题',
  description varchar(300) NOT NULL COMMENT '书单描述',
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='书单';

-- --------------------------------------------------------

--
-- 表的结构 booklist_wechat_user
--

CREATE TABLE booklist_wechat_user (
  booklist_id int(11) NOT NULL,
  wechat_user_id int(11) NOT NULL COMMENT '书单的收藏者（创建者也属于收藏者）',
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='书单-用户关系表';

-- --------------------------------------------------------

--
-- 表的结构 books
--

CREATE TABLE books (
  id int(11) NOT NULL,
  title varchar(100) NOT NULL,
  origin_title varchar(100) NOT NULL COMMENT '原标题',
  alt_title varchar(100) NOT NULL COMMENT '副标题',
  subtitle varchar(100) NOT NULL COMMENT '子标题',
  isbn varchar(20) NOT NULL COMMENT 'ISBN，唯一',
  language varchar(10) NOT NULL COMMENT '语种',
  publisher varchar(50) NOT NULL COMMENT '出版社',
  pubdate date DEFAULT NULL,
  class_num varchar(50) NOT NULL COMMENT '分类号',
  call_number varchar(20) NOT NULL COMMENT '索书号',
  author text NOT NULL COMMENT '作者，序列化数组',
  translator text NOT NULL COMMENT '译者，序列化数组',
  author_introduction text NOT NULL COMMENT '作者简介',
  translator_introduction text NOT NULL COMMENT '译者简介',
  binding varchar(11) NOT NULL COMMENT '装帧',
  price float NOT NULL COMMENT '价格',
  page int(11) NOT NULL COMMENT '页数',
  word int(11) NOT NULL COMMENT '字数',
  description text NOT NULL COMMENT '内容简介',
  catalog text NOT NULL COMMENT '目录',
  preview text NOT NULL COMMENT '导读',
  imgs text NOT NULL COMMENT 'small封面完整路径',
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='图书信息';

-- --------------------------------------------------------

--
-- 表的结构 book_booklist
--

CREATE TABLE book_booklist (
  booklist_id int(11) NOT NULL,
  book_id int(11) NOT NULL,
  comment text COMMENT '图书短评文本',
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='书单内图书';

-- --------------------------------------------------------

--
-- 表的结构 classifications
--

CREATE TABLE classifications (
  number varchar(10) NOT NULL COMMENT '分类号',
  name varchar(50) DEFAULT NULL COMMENT '分类名',
  son_number varchar(10) DEFAULT NULL COMMENT '第一个子分类号',
  parent_number varchar(10) DEFAULT NULL COMMENT '父分类号',
  next_number varchar(10) DEFAULT NULL COMMENT '下一个兄弟分类号',
  created_time datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='中图法图书分类号';

-- --------------------------------------------------------

--
-- 表的结构 codes
--

CREATE TABLE codes (
  id int(11) NOT NULL,
  phone varchar(15) NOT NULL,
  type varchar(10) NOT NULL COMMENT '验证码类型：wechat, library',
  code varchar(10) NOT NULL COMMENT '验证码',
  expiry int(5) NOT NULL DEFAULT '300' COMMENT '有效期，默认5分钟',
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='短信验证码';

-- --------------------------------------------------------

--
-- 表的结构 collections
--

CREATE TABLE collections (
  id int(11) NOT NULL,
  library_id int(11) NOT NULL,
  book_id int(11) NOT NULL,
  total_num int(11) NOT NULL COMMENT '图书总数',
  available_num int(11) NOT NULL COMMENT '可借数',
  is_available int(11) NOT NULL DEFAULT '1' COMMENT '是否可借',
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='馆藏信息';

-- --------------------------------------------------------

--
-- 表的结构 libraries
--

CREATE TABLE libraries (
  id int(11) NOT NULL,
  status int(11) NOT NULL DEFAULT '0' COMMENT '0~3 未审核，已通过，未通过，已拉黑',
  review_msg varchar(500) NOT NULL COMMENT '管理员驳回资质审核材料时给图书馆的简短说明',
  name varchar(50) NOT NULL COMMENT '图书馆名称',
  phone varchar(20) NOT NULL COMMENT '图书馆联系电话',
  address varchar(200) NOT NULL COMMENT '图书馆地址',
  introduction text NOT NULL COMMENT '图书馆简介',
  photos text NOT NULL COMMENT '图书馆照片链接的数组',
  qualifications text NOT NULL COMMENT '资质证明图片链接的数组',
  admin_phone varchar(20) NOT NULL COMMENT '管理员手机号',
  admin_name varchar(20) NOT NULL COMMENT '管理员姓名',
  admin_password varchar(20) NOT NULL COMMENT '管理员登录密码',
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='图书馆';

-- --------------------------------------------------------

--
-- 表的结构 orders
--

CREATE TABLE orders (
  id int(11) NOT NULL,
  status int(11) NOT NULL COMMENT '订单状态',
  wechat_user_id int(11) NOT NULL,
  isbn varchar(50) NOT NULL,
  library_id int(11) NOT NULL,
  should_take_time date DEFAULT NULL COMMENT '预订取书时间',
  actual_take_time varchar(20) DEFAULT NULL COMMENT '实际取书时间',
  renew_count int(11) DEFAULT '0' COMMENT '续借次数',
  should_return_time varchar(20) DEFAULT NULL COMMENT '应还时间',
  actual_return_time varchar(20) DEFAULT NULL COMMENT '实际还书时间',
  fine float NOT NULL DEFAULT '0' COMMENT '罚金数额',
  is_fine_paied int(11) NOT NULL DEFAULT '0' COMMENT '是否支付罚金',
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 recommended_book
--

CREATE TABLE recommended_book (
  id int(11) NOT NULL,
  wechat_user_id int(11) NOT NULL,
  book_id int(11) NOT NULL,
  comment varchar(300) NOT NULL COMMENT '推荐图书描述',
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='推荐图书表';

-- --------------------------------------------------------

--
-- 表的结构 recommended_booklist
--

CREATE TABLE recommended_booklist (
  id int(11) NOT NULL,
  wechat_user_id int(11) NOT NULL,
  booklist_id int(11) NOT NULL,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='推荐书单表';

-- --------------------------------------------------------

--
-- 表的结构 reviews
--

CREATE TABLE reviews (
  id int(11) NOT NULL,
  book_id int(11) NOT NULL,
  wechat_user_id int(11) NOT NULL,
  score int(11) DEFAULT NULL COMMENT '评分：1~10整数',
  content text NOT NULL COMMENT '评论内容',
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='图书评论';

-- --------------------------------------------------------

--
-- 表的结构 review_likes
--

CREATE TABLE review_likes (
  id int(11) NOT NULL,
  review_id int(11) NOT NULL,
  phone int(15) NOT NULL,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 tokens
--

CREATE TABLE tokens (
  id int(11) NOT NULL,
  token text NOT NULL,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  is_used int(11) NOT NULL DEFAULT '0' COMMENT '此token是否被使用'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='找回密码邮件内容';

-- --------------------------------------------------------

--
-- 表的结构 wechat_users
--

CREATE TABLE wechat_users (
  id int(11) NOT NULL,
  phone varchar(50) NOT NULL,
  openid varchar(50) NOT NULL COMMENT 'openid',
  status int(11) NOT NULL DEFAULT '0' COMMENT '0~3 未审核，已通过，未通过，已拉黑',
  review_msg varchar(200) NOT NULL COMMENT '管理员驳回资质审核材料时给图书馆的简短说明',
  nickname varchar(50) NOT NULL COMMENT '微信昵称',
  avatar varchar(200) NOT NULL COMMENT '头像链接',
  name varchar(100) NOT NULL COMMENT '真实姓名',
  address varchar(200) NOT NULL,
  birthday date DEFAULT NULL,
  id_number varchar(20) NOT NULL,
  id_card_img text NOT NULL COMMENT '身份证照片{front，back} JSON',
  postcode varchar(10) NOT NULL COMMENT '邮政编码',
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  deleted_at timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信用户';

--
-- Indexes for dumped tables
--

--
-- Indexes for table booklists
--
ALTER TABLE booklists
  ADD PRIMARY KEY (id);

--
-- Indexes for table booklist_wechat_user
--
ALTER TABLE booklist_wechat_user
  ADD PRIMARY KEY (booklist_id,wechat_user_id);

--
-- Indexes for table books
--
ALTER TABLE books
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY isbn_2 (isbn),
  ADD KEY isbn (isbn),
  ADD KEY class_num (class_num);
ALTER TABLE books ADD FULLTEXT KEY title (title);
ALTER TABLE books ADD FULLTEXT KEY origin_title (origin_title);
ALTER TABLE books ADD FULLTEXT KEY alt_title (alt_title);
ALTER TABLE books ADD FULLTEXT KEY subtitle (subtitle);

--
-- Indexes for table book_booklist
--
ALTER TABLE book_booklist
  ADD PRIMARY KEY (booklist_id,book_id);

--
-- Indexes for table classifications
--
ALTER TABLE classifications
  ADD PRIMARY KEY (number),
  ADD KEY parent_number (parent_number);

--
-- Indexes for table codes
--
ALTER TABLE codes
  ADD PRIMARY KEY (id);

--
-- Indexes for table collections
--
ALTER TABLE collections
  ADD PRIMARY KEY (id),
  ADD KEY lib_library_id (library_id),
  ADD KEY lib_library_id_2 (library_id);

--
-- Indexes for table libraries
--
ALTER TABLE libraries
  ADD PRIMARY KEY (id);

--
-- Indexes for table orders
--
ALTER TABLE orders
  ADD PRIMARY KEY (id),
  ADD KEY user_user_phone (wechat_user_id),
  ADD KEY state (status),
  ADD KEY create_time (created_at),
  ADD KEY bk_book_isbn (isbn);

--
-- Indexes for table recommended_book
--
ALTER TABLE recommended_book
  ADD PRIMARY KEY (id);

--
-- Indexes for table recommended_booklist
--
ALTER TABLE recommended_booklist
  ADD PRIMARY KEY (id);

--
-- Indexes for table reviews
--
ALTER TABLE reviews
  ADD PRIMARY KEY (id),
  ADD KEY bk_book_id (book_id),
  ADD KEY user_user_id (wechat_user_id);

--
-- Indexes for table review_likes
--
ALTER TABLE review_likes
  ADD PRIMARY KEY (id),
  ADD KEY user_user_id (phone),
  ADD KEY bk_review_id (review_id);

--
-- Indexes for table tokens
--
ALTER TABLE tokens
  ADD PRIMARY KEY (id);

--
-- Indexes for table wechat_users
--
ALTER TABLE wechat_users
  ADD PRIMARY KEY (id);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT booklists
--
ALTER TABLE booklists
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT books
--
ALTER TABLE books
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT codes
--
ALTER TABLE codes
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT collections
--
ALTER TABLE collections
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT libraries
--
ALTER TABLE libraries
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT orders
--
ALTER TABLE orders
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT recommended_book
--
ALTER TABLE recommended_book
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT recommended_booklist
--
ALTER TABLE recommended_booklist
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT reviews
--
ALTER TABLE reviews
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT review_likes
--
ALTER TABLE review_likes
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT tokens
--
ALTER TABLE tokens
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT wechat_users
--
ALTER TABLE wechat_users
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
