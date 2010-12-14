-- $Id$

DROP TABLE IF EXISTS `#__document`;
  
CREATE TABLE IF NOT EXISTS `#__document` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title`  varchar(255) NOT NULL  default '',
  `keywords`  varchar(255) NOT NULL default '',
  `description`	 varchar(255) NOT NULL default '',
  `author` varchar(255) NOT NULL default '',
  `alias`  varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `mime`  varchar(255) NOT NULL default '',
  `catid` int NOT NULL default'0',
  `created` date,
  `created_by` int NOT NULL default'0',
  `created_by_alias` varchar(255) NOT NULL,
  `modified` date default '0000-00-00 00:00:00' ,
  `modified_by` int NOT NULL default'0',
  `hits` int NOT NULL default'0',
  `params` varchar(65535) ,
  `language` char(7) NOT NULL DEFAULT '',
  `featured` tinyint(3),
  `ordering` int NOT NULL,
  `published` int NOT NULL default'0',
  `published_up` date,
  `published_down` date,
  `access` int NOT NULL default'0',
  `checked_out` int NOT NULL default'0',
  `checked_out_time` date default '0000-00-00 00:00:00',
     PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
 

