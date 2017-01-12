-- MySQL dump 10.13  Distrib 5.5.38, for debian-linux-gnu (x86_64)

--
-- Table structure for table `bol_user`
--

DROP TABLE IF EXISTS `bol_user`;
CREATE TABLE `bol_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` tinytext NOT NULL,
  `last_name` tinytext NOT NULL,
  `username` tinytext NOT NULL,
  `password` tinytext NOT NULL,
  `reset` tinytext,
  `reset_time` int(11) NOT NULL DEFAULT '0',
  `isadmin` tinyint(1) NOT NULL,
  `email` tinytext,
  `oauth2_login` tinytext,
  `created_time` int(11) NOT NULL DEFAULT '0',
  `last_login` int(11) NOT NULL DEFAULT '0',
  `warning_sent` int(11) NOT NULL DEFAULT '0',
  `isteacher` tinyint(1) NOT NULL DEFAULT '0',
  `preflang` tinytext NOT NULL,
  `family_name_first` tinyint(1) DEFAULT '0',
  `istranslator` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `bol_alphabet`
--

DROP TABLE IF EXISTS `bol_alphabet`;
CREATE TABLE `bol_alphabet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `direction` varchar(3) NOT NULL,
  `sample` tinytext NOT NULL,
  `english` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bol_alphabet`
--

INSERT INTO `bol_alphabet` VALUES
(1,'hebrew','rtl','בְּרֵאשִׁ֖ית בָּרָ֣א אֱלֹהִ֑ים אֵ֥ת הַשָּׁמַ֖יִם וְאֵ֥ת הָאָֽרֶץ','Hebrew'),
(2,'hebrew_translit','ltr','bᵊrēˀšît bārāˀ ʔᵉlōhîm ʔēt haššāmayim wᵊʔēt hāʔāreṣ','transliterated Hebrew'),
(3,'greek','ltr','Βίβλος γενέσεως Ἰησοῦ Χριστοῦ υἱοῦ Δαυεὶδ','Greek'),
(4,'latin','ltr','In principio creavit Deus caelum et terram.','Latin');

--
-- Table structure for table `bol_bible_refs`
--

DROP TABLE IF EXISTS `bol_bible_refs`;
CREATE TABLE `bol_bible_refs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `book` varchar(32) NOT NULL,
  `booknumber` int(11) NOT NULL,
  `chapter` int(11) NOT NULL,
  `verse` int(11) NOT NULL,
  `picture` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `bol_bible_urls`
--

DROP TABLE IF EXISTS `bol_bible_urls`;
CREATE TABLE `bol_bible_urls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `book` varchar(32) NOT NULL,
  `booknumber` int(11) NOT NULL,
  `chapter` int(11) NOT NULL,
  `verse` int(11) NOT NULL,
  `url` tinytext NOT NULL,
  `type` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `bol_class`
--

DROP TABLE IF EXISTS `bol_class`;
CREATE TABLE `bol_class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `classname` tinytext NOT NULL,
  `password` tinytext,
  `enrol_before` date DEFAULT NULL,
  `ownerid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `bol_classexercise`
--

DROP TABLE IF EXISTS `bol_classexercise`;
CREATE TABLE `bol_classexercise` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `classid` int(11) NOT NULL COMMENT 'A value of 0 means any class',
  `pathid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `bol_exercisedir`
--

DROP TABLE IF EXISTS `bol_exercisedir`;
CREATE TABLE `bol_exercisedir` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pathname` tinytext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `bol_font`
--

DROP TABLE IF EXISTS `bol_font`;
CREATE TABLE `bol_font` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `alphabet_id` int(11) NOT NULL,
  `font_family` tinytext NOT NULL,
  `text_size` int(11) NOT NULL,
  `text_italic` tinyint(1) NOT NULL,
  `text_bold` tinyint(1) NOT NULL,
  `feature_size` int(11) NOT NULL,
  `feature_italic` tinyint(1) NOT NULL,
  `feature_bold` tinyint(1) NOT NULL,
  `tooltip_size` int(11) NOT NULL,
  `tooltip_italic` tinyint(1) NOT NULL,
  `tooltip_bold` tinyint(1) NOT NULL,
  `input_size` int(11) NOT NULL,
  `input_italic` tinyint(1) NOT NULL,
  `input_bold` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bol_font`
--

INSERT INTO `bol_font` (`id`, `user_id`, `alphabet_id`, `font_family`, `text_size`, `text_italic`, `text_bold`, `feature_size`, `feature_italic`, `feature_bold`, `tooltip_size`, `tooltip_italic`, `tooltip_bold`, `input_size`, `input_italic`, `input_bold`) VALUES
(1, 0, 1, 'Ezra SIL Webfont, Times New Roman, Serif', 19, 0, 0, 14, 0, 0, 14, 0, 0, 14, 0, 0),
(2, 0, 2, 'Doulos SIL Webfont, Times New Roman, serif', 16, 0, 0, 14, 0, 0, 14, 0, 0, 14, 0, 0),
(3, 0, 3, 'Gentium Plus Webfont, Times New Roman, serif', 16, 0, 0, 14, 0, 0, 14, 0, 0, 14, 0, 0),
(4, 0, 4, 'Segoe UI, Arial, sans-serif', 16, 0, 0, 14, 0, 0, 14, 0, 0, 14, 0, 0);


--
-- Table structure for table `bol_personal_font`
--

DROP TABLE IF EXISTS `bol_personal_font`;
CREATE TABLE `bol_personal_font` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `alphabet_id` int(11) NOT NULL,
  `font_family` tinytext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ui` (`user_id`),
  CONSTRAINT `bol_personal_font_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `bol_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `bol_heb_urls`
--

DROP TABLE IF EXISTS `bol_heb_urls`;
CREATE TABLE `bol_heb_urls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lex` tinytext NOT NULL,
  `language` enum('Hebrew','Aramaic') NOT NULL,
  `url` text NOT NULL,
  `icon` tinytext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `bol_sta_displayfeature`
--

DROP TABLE IF EXISTS `bol_sta_displayfeature`;
CREATE TABLE `bol_sta_displayfeature` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `questid` int(11) NOT NULL,
  `qono` int(11) NOT NULL,
  `name` text NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ui` (`userid`),
  CONSTRAINT `bol_sta_displayfeature_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `bol_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `bol_sta_question`
--

DROP TABLE IF EXISTS `bol_sta_question`;
CREATE TABLE `bol_sta_question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `quizid` int(11) NOT NULL,
  `txt` text NOT NULL,
  `location` text NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ui` (`userid`),
  CONSTRAINT `bol_sta_question_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `bol_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `bol_sta_quiz`
--

DROP TABLE IF EXISTS `bol_sta_quiz`;
CREATE TABLE `bol_sta_quiz` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `templid` int(11) NOT NULL,
  `start` int(11) NOT NULL,
  `end` int(11) DEFAULT NULL,
  `valid` tinyint(1) NOT NULL,
  `grading` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ui` (`userid`),
  CONSTRAINT `bol_sta_quiz_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `bol_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Table structure for table `bol_sta_quiztemplate`
--

DROP TABLE IF EXISTS `bol_sta_quiztemplate`;
CREATE TABLE `bol_sta_quiztemplate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `pathname` text NOT NULL,
  `dbname` text,
  `dbpropname` text,
  `qoname` text,
  `quizcode` text,
  `quizcodehash` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ui` (`userid`),
  CONSTRAINT `bol_sta_quiztemplate_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `bol_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `bol_sta_requestfeature`
--

DROP TABLE IF EXISTS `bol_sta_requestfeature`;
CREATE TABLE `bol_sta_requestfeature` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `questid` int(11) DEFAULT NULL,
  `qono` int(11) DEFAULT NULL,
  `name` text NOT NULL,
  `value` text NOT NULL,
  `answer` text NOT NULL,
  `correct` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ui` (`userid`),
  CONSTRAINT `bol_sta_requestfeature_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `bol_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `bol_sta_universe`
--

DROP TABLE IF EXISTS `bol_sta_universe`;
CREATE TABLE `bol_sta_universe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `quizid` int(11) NOT NULL,
  `component` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ui` (`userid`),
  CONSTRAINT `bol_sta_universe_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `bol_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Table structure for table `bol_userclass`
--

DROP TABLE IF EXISTS `bol_userclass`;
CREATE TABLE `bol_userclass` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `classid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ui` (`userid`),
  KEY `ci` (`classid`),
  CONSTRAINT `bol_userclass_ibfk_2` FOREIGN KEY (`classid`) REFERENCES `bol_class` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `bol_userclass_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `bol_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `bol_userconfig`
--

DROP TABLE IF EXISTS `bol_userconfig`;
CREATE TABLE `bol_userconfig` (
  `user_id` int(11) NOT NULL,
  `usetooltip` tinyint(1) NOT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `bol_userconfig_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `bol_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `bol_exerciseowner`
--

DROP TABLE IF EXISTS `bol_exerciseowner`;
CREATE TABLE `bol_exerciseowner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pathname` text NOT NULL,
  `ownerid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `bol_migrations`
--

CREATE TABLE IF NOT EXISTS `bol_migrations` (
  `version` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bol_migrations`
--

INSERT INTO `bol_migrations` (`version`) VALUES
(2);
