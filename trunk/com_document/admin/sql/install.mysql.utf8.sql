-- $Id$

DROP TABLE IF EXISTS `#__document`;
 
CREATE TABLE `#__document` (
  `id` int() NOT NULL AUTO_INCREMENT,
  `title`  varchar(255) NOT NULL,
  `keywords`  varchar(255) NOT NULL,
  `description`	 varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `alias`  varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `mime`  varchar(255) NOT NULL,
  `catid` int()NOT NULL,
  `created` date,
  `created_by`int()NOT NULL,
  `created_by_alias` varchar(25) NOT NULL,
  `modified` date,
  `modified_by` int()NOT NULL,
  `hits` int()NOT NULL,
  `params` varchar(65535) ,
  `language` int() NOT NULL,
  `featured` tinyint(3),
  `ordering` int()NOT NULL,
  `published` int()NOT NULL,
  `published_up` date,
  `published_down` date,
  `access` int()NOT NULL,
  `checked_out` int()NOT NULL,
  `checked_out_time` date,
     PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
 

