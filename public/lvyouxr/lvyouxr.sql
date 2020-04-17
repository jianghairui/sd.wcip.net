/*
Navicat MySQL Data Transfer

Source Server         : MySQL5.7.17
Source Server Version : 50717
Source Host           : localhost:3306
Source Database       : lvyouxr

Target Server Type    : MYSQL
Target Server Version : 50717
File Encoding         : 65001

Date: 2017-06-04 16:18:15
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `allusers`
-- ----------------------------
DROP TABLE IF EXISTS `allusers`;
CREATE TABLE `allusers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `pwd` varchar(50) DEFAULT NULL,
  `cx` varchar(50) DEFAULT '普通管理员',
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=gb2312;

-- ----------------------------
-- Records of allusers
-- ----------------------------
INSERT INTO `allusers` VALUES ('2', 'hsg', 'hsg', '超级管理员', '2012-02-08 10:51:02');
INSERT INTO `allusers` VALUES ('7', 'hh', 'hh', '普通管理员', '2012-02-08 08:57:29');
INSERT INTO `allusers` VALUES ('8', 'fff', 'fff', '普通管理员', '2012-02-08 10:20:48');
INSERT INTO `allusers` VALUES ('9', 'ddd', 'ddd', '普通管理员', '2012-02-21 13:01:03');
INSERT INTO `allusers` VALUES ('10', 'gf', 'gf', '普通管理员', '2012-02-24 10:01:45');

-- ----------------------------
-- Table structure for `booktourline`
-- ----------------------------
DROP TABLE IF EXISTS `booktourline`;
CREATE TABLE `booktourline` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `linename` varchar(255) DEFAULT NULL,
  `price` varchar(20) DEFAULT NULL,
  `booktime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of booktourline
-- ----------------------------
INSERT INTO `booktourline` VALUES ('9', 'jiang', '姜海蕤', '布达拉宫七日游', '1780', '2017-06-01 00:50:44');
INSERT INTO `booktourline` VALUES ('10', 'jiang', '姜海蕤', '千岛湖两晶游', '450', '2017-06-01 00:50:46');
INSERT INTO `booktourline` VALUES ('13', 'fly', '希拉里', '布达拉宫七日游', '1780', '2017-06-01 09:26:59');
INSERT INTO `booktourline` VALUES ('16', 'hsg', '希拉里', '布达拉宫七日游', '1780', '2017-06-01 11:12:33');

-- ----------------------------
-- Table structure for `dx`
-- ----------------------------
DROP TABLE IF EXISTS `dx`;
CREATE TABLE `dx` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `leibie` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `content` text CHARACTER SET utf8,
  `addtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of dx
-- ----------------------------
INSERT INTO `dx` VALUES ('1', '系统简介', '<P><FONT face=宋体>&nbsp;&nbsp;&nbsp; 该旅游网站主要是给大家提供一个在线旅游服务的平台，朋友们可以通过我平台查看旅游线路，预订酒店，查询航班等信息，希望通过我平台可以给大家的出行带来方便，谢谢，其他介绍性的话语请您自己写几句吧，谢谢合作！</P>\r\n<P><FONT face=宋体>&nbsp;&nbsp;&nbsp; 该旅游网站主要是给大家提供一个在线旅游服务的平台，朋友们可以通过我平台查看旅游线路，预订酒店，查询航班等信息，希望通过我平台可以给大家的出行带来方便，谢谢，其他介绍性的话语请您自己写几句吧，谢谢合作！</P>\r\n<P><FONT face=宋体>&nbsp;&nbsp;&nbsp; 该旅游网站主要是给大家提供一个在线旅游服务的平台，朋友们可以通过我平台查看旅游线路，预订酒店，查询航班等信息，希望通过我平台可以给大家的出行带来方便，谢谢，其他介绍性的话语请您自己写几句吧，谢谢合作！</FONT></P></FONT></FONT>', '2012-02-24 00:47:51');
INSERT INTO `dx` VALUES ('2', '系统公告', '<p>最新公告：</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;xxxxxxxxxx</p><p>xxxx</p><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; xxx</p><p>xxxxxxxxxxxxxxxxxxx<img src=\"http://img.baidu.com/hi/jx2/j_0005.gif\"/><img src=\"http://img.baidu.com/hi/jx2/j_0057.gif\"/></p>', '2017-05-25 00:23:26');

-- ----------------------------
-- Table structure for `hangbanxinxi`
-- ----------------------------
DROP TABLE IF EXISTS `hangbanxinxi`;
CREATE TABLE `hangbanxinxi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `banjihao` varchar(50) DEFAULT NULL,
  `shifadi` varchar(50) DEFAULT NULL,
  `mudedi` varchar(50) DEFAULT NULL,
  `piaojia` varchar(50) DEFAULT NULL,
  `qifeishijian` varchar(50) DEFAULT NULL,
  `beizhu` varchar(500) DEFAULT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=gb2312;

-- ----------------------------
-- Records of hangbanxinxi
-- ----------------------------
INSERT INTO `hangbanxinxi` VALUES ('2', 'X4876', '温州', '上海', '420', '2017-06-05 06:14', 'gewgw', '2012-02-23 23:17:13');
INSERT INTO `hangbanxinxi` VALUES ('3', 'X5874', '杭州', '北京', '650', '2017-06-04 02:24', 'gewgw', '2012-02-23 23:17:38');
INSERT INTO `hangbanxinxi` VALUES ('4', 'H4784', '温州', '天津', '860', '2017-06-04 02:22', 'wegew', '2012-02-23 23:18:04');
INSERT INTO `hangbanxinxi` VALUES ('5', 'Y78352', '天津', '首尔', '2300', '2017-06-01 08:00', 'Here We Go', '2012-02-24 10:02:34');
INSERT INTO `hangbanxinxi` VALUES ('6', 'KFC100', '深圳', '天津', '960', '2017-06-04 02:21', '阿拉伯', '2017-06-04 00:37:23');

-- ----------------------------
-- Table structure for `jiudianxinxi`
-- ----------------------------
DROP TABLE IF EXISTS `jiudianxinxi`;
CREATE TABLE `jiudianxinxi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jiudianmingcheng` varchar(300) DEFAULT NULL,
  `xingji` varchar(50) DEFAULT NULL,
  `dianhua` varchar(50) DEFAULT NULL,
  `dizhi` varchar(300) DEFAULT NULL,
  `zhaopian` varchar(50) DEFAULT NULL,
  `beizhu` varchar(500) DEFAULT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=gb2312;

-- ----------------------------
-- Records of jiudianxinxi
-- ----------------------------
INSERT INTO `jiudianxinxi` VALUES ('2', '名豪大酒店', '五星级', '32523532', '灵溪人民路3号', 'uploadfile/6.jpg', 'gewgweg', '2012-02-23 23:24:59');
INSERT INTO `jiudianxinxi` VALUES ('3', '万宝路大酒店', '四星级', '56489456', '西五街', 'uploadfile/7.jpg', 'fwefew', '2012-02-23 23:32:19');
INSERT INTO `jiudianxinxi` VALUES ('4', '好联国际酒店', '五星级', '23523532', '湖影大道', 'uploadfile/8.jpg', 'gewgew', '2012-02-23 23:32:52');
INSERT INTO `jiudianxinxi` VALUES ('5', '诚大饭店', '五星级', '3523523', '西三街龙金大道', 'uploadfile/9.jpg', 'gewgew', '2012-02-24 00:38:35');
INSERT INTO `jiudianxinxi` VALUES ('6', 'hrehre', '三星级', '2353232', 'gwegwegew', 'uploadfile/13.jpg', 'gewgwe', '2012-02-24 10:02:54');
INSERT INTO `jiudianxinxi` VALUES ('7', '希尔顿酒店第一', '五星级', '011-20220202', '帕里斯希尔顿', 'uploadfile/18.jpg', '瓦湖', '2017-05-24 21:38:59');
INSERT INTO `jiudianxinxi` VALUES ('8', 'xierdun', '五星级', '119', '帕里斯希尔顿', 'uploadfile/17.jpg', '成功了，啊哈哈', '2017-05-24 23:16:09');
INSERT INTO `jiudianxinxi` VALUES ('9', '顶顶顶顶', '五星级', '100', '去', 'uploadfile/2.png', '去', '2017-06-04 00:39:01');

-- ----------------------------
-- Table structure for `jiudianyuding`
-- ----------------------------
DROP TABLE IF EXISTS `jiudianyuding`;
CREATE TABLE `jiudianyuding` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jiudianmingcheng` varchar(300) DEFAULT NULL,
  `xingji` varchar(50) DEFAULT NULL,
  `dianhua` varchar(50) DEFAULT NULL,
  `dizhi` varchar(300) DEFAULT NULL,
  `yudingren` varchar(50) DEFAULT NULL,
  `yudingshijian` varchar(50) DEFAULT NULL,
  `yudingrenshu` varchar(50) DEFAULT NULL,
  `beizhu` varchar(500) DEFAULT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `issh` varchar(10) DEFAULT '否',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=gb2312;

-- ----------------------------
-- Records of jiudianyuding
-- ----------------------------
INSERT INTO `jiudianyuding` VALUES ('2', '诚大饭店', '五星级', '3523523', '西三街龙金大道', '555', '2012-02-28', '3', 'ewgw', '2012-02-24 01:11:28', '否');
INSERT INTO `jiudianyuding` VALUES ('3', '诚大饭店', '五星级', '3523523', '西三街龙金大道', '555', '2012-02-29', '3', 'ewegw', '2012-02-24 01:12:02', '是');
INSERT INTO `jiudianyuding` VALUES ('4', '诚大饭店', '五星级', '3523523', '西三街龙金大道', 'fs', '2017-05-01 02:53', '4', 'gwgw', '2012-02-24 10:01:14', '是');
INSERT INTO `jiudianyuding` VALUES ('5', '希尔顿酒店第一', '五星级', '011-20220202', '帕里斯希尔顿', 'fly', '2017-06-01 02:52', '12', '', '2017-06-01 09:37:31', '否');
INSERT INTO `jiudianyuding` VALUES ('6', '希尔顿酒店第一', '五星级', '011-20220202', '帕里斯希尔顿', 'jiang', '2017-06-04 02:51', '2', '', '2017-06-03 17:04:07', '是');
INSERT INTO `jiudianyuding` VALUES ('7', '希尔顿酒店第一', '五星级', '011-20220202', '帕里斯希尔顿', 'jiang', '2017-06-04 02:57', '2', '', '2017-06-04 02:57:10', '否');

-- ----------------------------
-- Table structure for `liuyanban`
-- ----------------------------
DROP TABLE IF EXISTS `liuyanban`;
CREATE TABLE `liuyanban` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zhanghao` varchar(50) DEFAULT NULL,
  `zhaopian` varchar(50) DEFAULT NULL,
  `xingming` varchar(50) DEFAULT NULL,
  `liuyan` varchar(50) DEFAULT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `huifu` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=gb2312;

-- ----------------------------
-- Records of liuyanban
-- ----------------------------
INSERT INTO `liuyanban` VALUES ('15', '555', 'uploadfile/8.jpg', '555', 'rehreher', '2012-02-20 21:51:05', 'gsdgsd');
INSERT INTO `liuyanban` VALUES ('16', '555', 'uploadfile/8.jpg', '555', 'herhfdjd', '2012-02-20 21:51:09', 'jfgjfg');
INSERT INTO `liuyanban` VALUES ('18', '555', 'uploadfile/8.jpg', '555', 'fff', '2012-02-24 00:45:32', 'fwefwe');
INSERT INTO `liuyanban` VALUES ('19', 'fs', 'uploadfile/1.gif', 'fs', 'hdfhdf', '2012-02-24 10:01:04', 'fdfds');
INSERT INTO `liuyanban` VALUES ('20', 'jiang', 'uploadfile/22.jpg', '姜海蕤', '这网站很古老', '2017-05-31 23:18:31', null);
INSERT INTO `liuyanban` VALUES ('21', 'jiang', 'uploadfile/22.jpg', '姜海蕤', 'I am Good', '2017-06-04 11:43:52', null);
INSERT INTO `liuyanban` VALUES ('22', 'jiang', 'uploadfile/22.jpg', '姜海蕤', '雅虎', '2017-06-04 11:44:03', null);

-- ----------------------------
-- Table structure for `lvyouxianlu`
-- ----------------------------
DROP TABLE IF EXISTS `lvyouxianlu`;
CREATE TABLE `lvyouxianlu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bianhao` varchar(50) DEFAULT NULL,
  `mingcheng` varchar(300) DEFAULT NULL,
  `chufadi` varchar(50) DEFAULT NULL,
  `mudedi` varchar(50) DEFAULT NULL,
  `chuxingshijian` varchar(50) DEFAULT NULL,
  `jiage` varchar(50) DEFAULT NULL,
  `chuxingshichang` varchar(50) DEFAULT NULL,
  `jiaotonggongju` varchar(50) DEFAULT NULL,
  `beizhu` varchar(500) DEFAULT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=gb2312;

-- ----------------------------
-- Records of lvyouxianlu
-- ----------------------------
INSERT INTO `lvyouxianlu` VALUES ('2', '101', '千岛湖两晶游', '温州', '千岛湖', '2012-02-24', '450', '2', '汽车', 'gwgwe', '2012-02-23 21:29:06');
INSERT INTO `lvyouxianlu` VALUES ('3', '102', '布达拉宫七日游', '杭州', '布达拉宫', '2012-02-25', '1780', '7', '火车', 'ggt32', '2012-02-23 22:28:48');
INSERT INTO `lvyouxianlu` VALUES ('4', '103', '巴厘岛-日本', '巴厘岛', '日本', '2017-06-04', '50', '7', '飞机', 'wegewg', '2012-02-24 10:02:17');

-- ----------------------------
-- Table structure for `toupiaojilu`
-- ----------------------------
DROP TABLE IF EXISTS `toupiaojilu`;
CREATE TABLE `toupiaojilu` (
  `lid` int(11) DEFAULT NULL,
  `username` varchar(10) DEFAULT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=gb2312;

-- ----------------------------
-- Records of toupiaojilu
-- ----------------------------
INSERT INTO `toupiaojilu` VALUES ('2', 'jiang', '2017-06-04 16:05:58');
INSERT INTO `toupiaojilu` VALUES ('3', 'jiang', '2017-06-04 16:06:43');
INSERT INTO `toupiaojilu` VALUES ('4', 'jiang', '2017-06-04 16:06:46');
INSERT INTO `toupiaojilu` VALUES ('2', 'sky', '2017-06-04 16:16:03');

-- ----------------------------
-- Table structure for `xinwentongzhi`
-- ----------------------------
DROP TABLE IF EXISTS `xinwentongzhi`;
CREATE TABLE `xinwentongzhi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `biaoti` varchar(500) CHARACTER SET gb2312 DEFAULT NULL,
  `leibie` varchar(50) CHARACTER SET gb2312 DEFAULT NULL,
  `neirong` text CHARACTER SET gb2312,
  `tianjiaren` varchar(50) CHARACTER SET gb2312 DEFAULT NULL,
  `addtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `shouyetupian` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `dianjilv` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of xinwentongzhi
-- ----------------------------
INSERT INTO `xinwentongzhi` VALUES ('48', '西藏将迎来大范围降雪天气 ', '站内新闻', '新华网拉萨３月７日专电（文涛、拉巴卓玛）记者７日从西藏自治区气象局获悉，９日起西藏将迎来大范围降雪天气，全区自西向东将有４～８摄氏度降温，藏北及南部边缘地区的部分地方还将出现８级左右大风天气。 \r\n<P>　　西藏自治区气象局预报显示，本周三至周五，阿里地区和日喀则地区南部有小到中雪，那曲地区、昌都地区和林芝地区多云有小雪（雨）。周末，那曲地区东部、昌都地区和林芝地区多云间阴天，部分地方有小雪（雨）。全区自西向东有４～８摄氏度降温。周内藏北一带和南部边缘地区有８级左右的大风。</P>\r\n<P>　　气象专家提醒：因此前阿里地区和日喀则地区南部降雪天气造成路面积雪尚未融化，周内上述地区还将出现降雪，因此提醒有关部门要加强交通安全管理，过往车辆要谨慎行驶，确保安全。由于本周全区有中等强度降温，提醒各界群众在生产生活中注意天气变化，及时增添衣物，注意防寒保暖，防止发生感冒等疾病。</P>\r\n<P>　　此外，近期风干物燥，气象火险等级较高，提醒相关部门注意森林、草原及居民区用火安全。此间气象部门还特别提醒，当前西藏各地都在欢庆藏历年，居民要注意防范燃放烟花爆竹引起的火灾。</P><!-- end_ct -->', 'hsg', '2012-02-23 21:15:45', 'uploadfile/1.jpg', '5');
INSERT INTO `xinwentongzhi` VALUES ('49', '太阳风暴点亮北极激发壮观极光', '站内新闻', '<P>北京时间2月22日消息，据国家地理杂志网站报道，这组新的极光照片显示，2月14日，情人节上演极光秀，太阳风暴照亮北极之夜。 \r\n<P>　<STRONG>　1.放电情人节</STRONG></P>\r\n<DIV class=img_wrapper><IMG title=\"放电情人节(图片提供：&Oslash;ystein Lunde Ingvaldsen)\" alt=\"放电情人节(图片提供：&Oslash;ystein Lunde Ingvaldsen)\" src=\"http://i0.sinaimg.cn/IT/2011/0222/U2727P2DT20110222091328.jpg\"><SPAN class=img_descr>放电情人节(图片提供：&Oslash;ystein Lunde Ingvaldsen)</SPAN></DIV>\r\n<P>　　虽然太阳和地球相守已经有45亿年了，但是它们的爱情看起来依旧放电，情人节出现的北极光色彩秀就是证明。上图是挪威维斯特罗伦上空闪过的短暂而眩目的极光。当太阳发出的大量带电粒子流向地球时就会上演这类奇观。最近，周日(20日)开始的所谓日冕物质喷射使得这周的极光活动增加。</P>\r\n<P>　<STRONG>　2.极光拥抱</STRONG></P>\r\n<DIV class=img_wrapper><IMG title=\"极光拥抱(图片提供：Chad Blakley)\" style=\"WIDTH: 500px; HEIGHT: 331px\" alt=\"极光拥抱(图片提供：Chad Blakley)\" src=\"http://i3.sinaimg.cn/IT/2011/0222/U2727P2DT20110222091448.jpg\"><SPAN class=img_descr>极光拥抱(图片提供：Chad Blakley)</SPAN></DIV>\r\n<P>　　2月14日北极光笼罩着瑞典阿比斯库国家公园的山地。虽然在大多数人眼里，极光仍有些神秘，但极光实际上是很常见的现象，在北半球和南半球高纬度地区有规律地出现。但是，对着地球的大太阳风暴可能会引发不同寻常的自然奇观，有时，形成的极光在很多区域都能看到。</P>', 'hsg', '2012-02-23 21:16:42', 'uploadfile/2.jpg', '3');
INSERT INTO `xinwentongzhi` VALUES ('50', '从印加遗址到巨石阵', '站内新闻', '<P>北京时间2月23日消息，当今世界有许多充满活力的大都市，例如东京、芝加哥、纽约和迪拜，但早在几千年前，这个世界上就出现了伟大的城市和伟大的文明了，有些是建立在宗教信仰之上，有些是建立在政权之上。虽然现在这些令人难以置信的城市只剩下一片片废墟，但它们展现了独特的古代建筑艺术和一些历史上最杰出的技艺。以下是国外一家博客网站盘点的全球十三座最令人难以置信的失落古城。 \r\n<P><STRONG>　　1.印度桑吉</STRONG></P>\r\n<DIV class=img_wrapper><IMG title=印度桑吉 alt=印度桑吉 src=\"http://i0.sinaimg.cn/IT/2011/0223/U5385P2DT20110223082215.jpg\"><SPAN class=img_descr>印度桑吉</SPAN></DIV>\r\n<P>　　桑吉是印度一座不朽的历史遗迹，其上有50座佛塔。其中最著名的是桑奇大塔，它是保存最完好的一座早期佛塔和世界最精美的一座建筑物。这些巨大的佛塔其实是巨大的半圆形球形结构，它中央的小室用来盛放佛陀的遗物。桑奇大塔还是佛陀由生到死的人生轮回的象征。桑吉是佛教艺术和建筑学经历兴起、发展和死亡的地方。</P>\r\n<P><STRONG>　　2.秘鲁马丘比丘</STRONG></P>\r\n<DIV class=img_wrapper><IMG title=秘鲁马丘比丘 alt=秘鲁马丘比丘 src=\"http://i2.sinaimg.cn/IT/2011/0223/U5385P2DT20110223082226.jpg\"><SPAN class=img_descr>秘鲁马丘比丘</SPAN></DIV>\r\n<P>　　神秘的马丘比丘遗址可能是世界上最让人捉摸不透的谜一样的古代遗址。它是一座新大陆发现之前就存在的印加遗址，位于秘鲁印加圣谷上方的山脊上。它的建筑风格属于古典印加风格，周围分布着很多天然温泉、梯田、很多寺庙、储藏室和其他美丽的宫殿。整个马丘比丘城令人赏心悦目，感觉像个绿色天堂。</P>', 'hsg', '2012-02-23 21:18:25', 'uploadfile/3.jpg', '1');
INSERT INTO `xinwentongzhi` VALUES ('51', '元宵节杭州周边好去处', '站内新闻', '<P align=center><IMG style=\"BORDER-LEFT-COLOR: #000000; BORDER-BOTTOM-COLOR: #000000; BORDER-TOP-COLOR: #000000; BORDER-RIGHT-COLOR: #000000\" src=\"http://www.cnqk.gov.cn/upload/editorfiles/2011.2.17_10.39.30_5268.jpg\" border=0><BR>　重回儿时提灯走桥的浪漫</P>\r\n<P>　　当人们还沉浸在春节欢乐气氛中时，元宵佳节又将接踵而至。元宵节最好玩的莫过于热闹非凡的元宵灯会，形象逼真、形态各异的彩灯观赏和猜谜、打麻酥糖、提灯走桥等民俗活动让人充满期待。 <BR><BR>　　元宵节作为兔年春节最后一个重要节日，杭州周边各地结合当地民俗推出了各具特色的元宵灯会迎接游客的到来。 <BR><BR>　　<STRONG>提灯走桥 星星点点的浪漫 <BR><BR></STRONG>　　夜色暗下来，乌镇小巷里亮起点点灯火，男女老少提着花灯在河边、桥上游走，汇成“灯火长龙”，远远看去，煞是壮观。这就是乌镇元宵节“提灯走桥”风俗。走桥也是有讲究的，必须走过至少十座桥，忌走回头桥，保佑来年平安。 <BR><BR>　　据乌镇景区相关负责人王小姐介绍，乌镇人一向就有元宵节走桥办灯会、猜谜吃汤圆的习惯。而今年的元宵节与往年相比，更融入了新的内涵。来到西栅的游客，不妨去传统的老街花灯铺，买上一盏手工花灯，跟着老师傅学做一盏属于你的手工花灯，提着花灯去走一走西栅各式各样的古桥。如果住下来，还可以和当地人一起过元宵节，和房东一起做元宵。 <BR><BR></P>', 'hsg', '2012-02-23 21:19:21', 'uploadfile/4.jpg', '2');
INSERT INTO `xinwentongzhi` VALUES ('52', '温州春节境外游火爆', '站内新闻', '今天，记者从温州市部分旅行社了解到，春节旅游境外游相对火爆，市民选择旅游产品更趋高端化；而国内游则稍显平淡。 \r\n<P>　　“元旦过后，澳洲的海岛线基本上已经售光。”受冷冬影响，春节旅游境外游主要集中在气候暖和的地方，东南亚、澳洲的线路基本卖光，温州精诚国旅营销总监陈良辰告诉记者，与此形成反差的是，今年日本和韩国的线路仍有部分余额。</P><!--advertisement code begin--><!--advertisement code end-->\r\n<P>　　在出境游方面海岛线路最受市民追捧，马尔代夫、普吉岛、巴厘岛分别为三大热门旅游地，其中又以马尔代夫最火热。陈良辰说，今年春节前往马尔代夫的旅游团规模基本都在2、30人左右。</P>\r\n<P>　　今年春节旅游市场的另一特点是，市民在选择旅游产品时更趋高端化，一家人出行动不动就是十几万，住宿更是要选择国际五星级酒店。</P>\r\n<P>　　相较于境外游的高端和火爆，国内游则明显不给力。记者从温州市几大旅行社了解到，至目前为止国内常规线路与往年基本持平，略有下降；而短途旅游，几大旅行社均表示不理想，其中一家旅行社仅售出2成，往年这个时候已经卖出5成多</P>', 'hsg', '2012-02-23 21:19:49', 'uploadfile/5.jpg', '12');
INSERT INTO `xinwentongzhi` VALUES ('53', '测试图片2', '站内新闻', 'hfdhfd', '姜海蕤', '2012-02-24 10:01:56', 'uploadfile/12.jpg', '6');
INSERT INTO `xinwentongzhi` VALUES ('55', '测试新闻二', '站内新闻', '<p>摔跤吧，爸爸！<br/></p>', 'fff', '2017-05-24 21:18:24', 'uploadfile/21.jpg', '3');
INSERT INTO `xinwentongzhi` VALUES ('56', '啊啊', '站内新闻', '<p>FUCK<br/></p>', 'fff', '2017-05-25 00:03:08', 'uploadfile/1.png', '6');

-- ----------------------------
-- Table structure for `yonghuzhuce`
-- ----------------------------
DROP TABLE IF EXISTS `yonghuzhuce`;
CREATE TABLE `yonghuzhuce` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zhanghao` varchar(50) DEFAULT NULL,
  `mima` varchar(50) DEFAULT NULL,
  `xingming` varchar(50) DEFAULT NULL,
  `xingbie` varchar(50) DEFAULT NULL,
  `diqu` varchar(50) DEFAULT NULL,
  `Email` varchar(50) DEFAULT NULL,
  `zhaopian` varchar(50) DEFAULT NULL,
  `addtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `issh` varchar(10) DEFAULT '否',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=gb2312;

-- ----------------------------
-- Records of yonghuzhuce
-- ----------------------------
INSERT INTO `yonghuzhuce` VALUES ('17', 'fly', 'fly', '希拉里', '女', '浙江', '475474', 'uploadfile/11.jpg', '2012-02-20 21:49:16', '是');
INSERT INTO `yonghuzhuce` VALUES ('18', 'sky', 'sky', '奥巴马', '男', '浙江', 'twq@163.com', 'uploadfile/10.jpg', '2012-02-21 13:00:11', '是');
INSERT INTO `yonghuzhuce` VALUES ('19', 'fashi', 'fashi', '特朗普', '男', '浙江', 'sdfs@163.com', 'uploadfile/1.gif', '2012-02-24 10:00:17', '是');
INSERT INTO `yonghuzhuce` VALUES ('20', 'jiang', 'jiang', '姜海蕤', '男', '浙江', '1873645345@qq.com', 'uploadfile/22.jpg', '2017-05-31 23:11:11', '是');

-- ----------------------------
-- Table structure for `youqinglianjie`
-- ----------------------------
DROP TABLE IF EXISTS `youqinglianjie`;
CREATE TABLE `youqinglianjie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wangzhanmingcheng` varchar(50) DEFAULT NULL,
  `wangzhi` varchar(50) DEFAULT NULL,
  `addtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=gb2312;

-- ----------------------------
-- Records of youqinglianjie
-- ----------------------------
INSERT INTO `youqinglianjie` VALUES ('11', '中国百度', 'http://www.by960.cn', '2012-02-08 14:47:19');
INSERT INTO `youqinglianjie` VALUES ('12', '中国网易', 'http://www.zgyimin.cn', '2012-02-08 14:47:30');
INSERT INTO `youqinglianjie` VALUES ('13', '中国新浪', 'http://www.bisow.cn', '2012-02-08 14:47:45');
INSERT INTO `youqinglianjie` VALUES ('14', '中国雅虎', 'http://www.ccbysj.cn', '2012-02-08 14:47:57');
INSERT INTO `youqinglianjie` VALUES ('15', '阿里巴巴中国', 'http://www.zgziliao.cn', '2012-02-08 14:48:15');
