-- phpMyAdmin SQL Dump
-- version 4.7.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 2018-06-02 17:25:18
-- 服务器版本： 5.6.33-0ubuntu0.14.04.1
-- PHP Version: 7.0.30-1+ubuntu14.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library`
--

-- --------------------------------------------------------

--
-- 表的结构 `libraries`
--

CREATE TABLE `libraries` (
  `id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0~3 未审核，已通过，未通过，已拉黑',
  `review_msg` varchar(500) NOT NULL COMMENT '管理员驳回资质审核材料时给图书馆的简短说明',
  `name` varchar(50) NOT NULL COMMENT '图书馆名称',
  `phone` varchar(20) NOT NULL COMMENT '图书馆联系电话',
  `address` varchar(200) NOT NULL COMMENT '图书馆地址',
  `introduction` text NOT NULL COMMENT '图书馆简介',
  `photos` text NOT NULL COMMENT '图书馆照片链接的数组',
  `qualifications` text NOT NULL COMMENT '资质证明图片链接的数组',
  `admin_phone` varchar(20) NOT NULL COMMENT '管理员手机号',
  `admin_name` varchar(20) NOT NULL COMMENT '管理员姓名',
  `admin_password` varchar(20) NOT NULL COMMENT '管理员登录密码',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='图书馆';

--
-- 转存表中的数据 `libraries`
--

INSERT INTO `libraries` (`id`, `status`, `review_msg`, `name`, `phone`, `address`, `introduction`, `photos`, `qualifications`, `admin_phone`, `admin_name`, `admin_password`, `created_at`, `updated_at`) VALUES
(1, 0, '', '测试图书馆', '13024121231', '北京市朝阳区', '图书馆是一个收藏资讯、原始资料、资料库并提供相关服务的地方，可以由公共团体、政府机构或者个人组织开办。图书馆在人类文明发展及历史存流具显著作用，乃人类智慧的宝库。\r\n在传统意义上，图书馆是收藏书和各种出版物的地方。然而，现在资料保存已经不止是保存图书，许多图书馆把地图、印刷物，或者其他档案和艺术作品保存在各种载体上，比如微缩胶片、磁带、CD、LP、盒式磁带、录像带和DVD。图书馆通过访问CD-ROM、订购数据库和互联网提供服务。因此，人们渐渐把现代图书馆重新定义为能够无限制得获取多种来源、多种格式的信息。除了提供资源，图书馆还有专家和图书馆员来提供服务，他们善于寻找和组织信息，并解释信息需求。近些年，人们对图书馆的理解已经超越了建筑的围墙，读者可以用电子工具获得资源，图书馆员用各种数字工具来引导读者和分析海量知识。图书馆的另一作用是收藏人们不愿意购买（或者无力购买）的资源，作为馆藏提供大众使用或查阅。', '[     \"http://n1-q.mafengwo.net/s7/M00/8B/BE/wKgB6lRs4X-AJ4pOACX-OTdBrLk34.jpeg?imageMogr2%2Fthumbnail%2F%21690x370r%2Fgravity%2FCenter%2Fcrop%2F%21690x370%2Fquality%2F100\"   ]', '', '', '', '', '2018-05-05 19:52:50', '2018-05-05 19:58:11'),
(2, 1, '', '北京大学图书馆', '13001230123', '北京市海淀区北京大学', '', '', '', '', '', '', '2018-05-20 17:04:11', '2018-05-20 17:04:11'),
(3, 0, '', '东城区图书馆', ' +86 10 6405 1155', '中国北京市东城区交道口东大街85号', '北京市东城区图书馆成立于1956年，是区政府兴办的综合性公共图书馆，是收集、整理文献，并向社会公众提供文献服务的公益性文化教育机构，是北京市精神文明先进单位。2013年，东城区图书馆更名为东城区第一图书馆。1998年、2003年、2009年、2013年被文化部评为地市级一级图书馆。2012年，获得“全民阅读推广示范基地”及“全国人文社科普及基地”称号；“书海听涛系列读者活动”荣获北京市学习品牌；荣获文化部颁发的“电子阅览室示范点”称号。2013年，图书馆荣获“第三届北京阅读季先进单位”称号。\r\n\r\n　　现图书馆大楼使用于1996年，位于交通便利的交道口东大街85号，总面积11780平方米，它北倚国子监街，南眺文天祥祠、顺天府学，东连北京22中，西接东方文化交流中心而与文化馆、钟鼓楼相望，文化地理环境极为优越。\r\n\r\n　　2007年10月至2008年5月，为了进一步改善读者借阅环境，提升图书馆服务水平与能力，更好地发挥其在公共文化服务体系建设中的作用，区政府投资3000余万元，对大楼进行了升级改造，现馆内设有第一外借室、第二外借室、少儿借阅室、综合阅览室、外文阅览室、创意文献阅览室、地方文献阅览室、政府信息查阅室、自习室、视障阅览室10个服务窗口，拥有各类文献近60万册（件），阅览座位700席，可上网电脑100多台，每年接待社会公众40余万人次，外借书刊30万册次以上，组织读者活动300余场次。\r\n\r\n　　东城区图书馆会议中心内还拥有典雅庄重、温馨舒适的影剧院、展览厅、报告厅、多功能厅、会议室、培训教室，可向社会公众提供多种类型、层次的文化教育服务。\r\n\r\n　　东城区图书馆的工作目标是通过追求文献利用率的最大化、追求读者满意度的最大化，从而实现图书馆社会文化价值的最大化，使之成为全国一流的、独具特色的、具有现代化管理水平的区级图书馆。', '[]', '', '461754', '测试用户', '123456', '2018-06-02 17:14:49', '2018-06-02 17:14:49'),
(4, 1, '', '国家图书馆', '4006006988', '北京市中关村南大街33号', '国家图书馆', '[]', '', '12345678', '测试用户', '123456', '2018-06-02 17:16:00', '2018-06-02 17:16:00'),
(5, 0, '', '北京航空航天大学图书馆', '+86 10 8231 7074', '中国北京市海淀区五道口学院路37号 邮政编码: 100083', '暂无', '', '', '', '', '', '2018-06-02 17:16:30', '2018-06-02 17:16:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `libraries`
--
ALTER TABLE `libraries`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `libraries`
--
ALTER TABLE `libraries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
