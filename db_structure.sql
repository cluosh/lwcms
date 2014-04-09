SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = '+02:00';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `lwCMS_mod_inlineforms`;
CREATE TABLE `lwCMS_mod_inlineforms` (
  `form` varchar(30) NOT NULL,
  `fields` text NOT NULL,
  `checks` text NOT NULL,
  PRIMARY KEY (`form`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `lwCMS_mod_inlineforms_data`;
CREATE TABLE `lwCMS_mod_inlineforms_data` (
  `form` varchar(30) NOT NULL,
  `data` text NOT NULL,
  `timestamp` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `lwCMS_mod_inlineforms_info`;
CREATE TABLE `lwCMS_mod_inlineforms_info` (
  `form` varchar(30) NOT NULL,
  `info` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `lwCMS_pages`;
CREATE TABLE `lwCMS_pages` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `pageID_string` char(20) NOT NULL,
  `page_title` char(50) NOT NULL,
  `theme_name` char(20) DEFAULT NULL,
  `template_name` char(20) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `lwCMS_pages_content`;
CREATE TABLE `lwCMS_pages_content` (
  `pageID` int(11) DEFAULT NULL,
  `contentArea` char(40) NOT NULL,
  `contentType` varchar(30) NOT NULL,
  `content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `lwCMS_users`;
CREATE TABLE `lwCMS_users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(40) NOT NULL,
  `passhash` char(40) NOT NULL,
  `level` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `lwCMS_users` (`ID`, `name`, `passhash`, `level`) VALUES
(1,	'admin',	'dd94709528bb1c83d08f3088d4043f4742891f4f',	0);
