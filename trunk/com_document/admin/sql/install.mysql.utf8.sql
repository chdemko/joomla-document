-- $Id$

DROP TABLE IF EXISTS `#__document`;
  
CREATE TABLE IF NOT EXISTS `#__document` (
  `id` int NOT NULL AUTO_INCREMENT,
  `asset_id` INTEGER UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
  `title`  varchar(255) NOT NULL  default '' COMMENT 'Title of the document',
  `keywords`  varchar(255) NOT NULL default '' COMMENT ' Keyword was representing the Document',
  `description`	 varchar(255) NOT NULL default '' COMMENT 'The Description of the Document',
  `author` varchar(255) NOT NULL default '' COMMENT 'The author of the Document ',
  `alias`  varchar(255) NOT NULL default '' COMMENT 'A string representing the alias of Document',
  `filename` varchar(255) NOT NULL default '' COMMENT 'Document link on the installation of joomla',
  `mime`  varchar(255) NOT NULL default '' COMMENT 'classification of types of files on the Internet',
  `catid` int NOT NULL default'0' COMMENT 'The number of category',
  `created` date default '0000-00-00 00:00:00'COMMENT 'Creating date of the document',
  `created_by` int NOT NULL default'0' COMMENT 'the number of user who created the document' ,
  `created_by_alias` varchar(255) NOT NULL COMMENT 'String representing an alias for the author ',
  `modified` date default '0000-00-00 00:00:00' COMMENT 'Date of modification',
  `modified_by` int NOT NULL default'0' COMMENT 'The user who modified the document',
  `hits` int NOT NULL default'0' COMMENT 'Number of clics to laod document',
  `params` varchar(65535) COMMENT 'string that can store additional settings  ',
  `language` char(7) NOT NULL DEFAULT '' ,
  `featured` tinyint(3) COMMENT 'Bolean indicating that the document is higtligted',
  `ordering` int NOT NULL COMMENT 'The serial number of a document within a category ',
  `published` int NOT NULL default'0' COMMENT 'Integer indicated document status ',
  `published_up` date COMMENT 'Date of the publishing of a document',
  `published_down` date COMMENT 'date of th unpublishing of a document',
  `access` int NOT NULL default'0' COMMENT 'the minimum level to access to a document',
  `checked_out` int NOT NULL default'0' COMMENT 'representing the user who is currently editing the document',
  `checked_out_time` date default '0000-00-00 00:00:00' COMMENT 'date representing the start time of plushing',
     PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;


