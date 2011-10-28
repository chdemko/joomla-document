CREATE TABLE IF NOT EXISTS `#__document` (
	`id`
		INTEGER
		NOT NULL
		AUTO_INCREMENT
		COMMENT 'PK of this table',
	`version`
		INTEGER UNSIGNED
		NOT NULL
		DEFAULT '0'
		COMMENT 'Version number',
	`max_version`
		INTEGER UNSIGNED
		NOT NULL
		DEFAULT '0'
		COMMENT 'Maximum version number',
	`asset_id`
		INTEGER UNSIGNED
		NOT NULL
		DEFAULT '0'
		COMMENT 'FK to the #__assets table',
	`title`
		VARCHAR(255)
		NOT NULL
		DEFAULT ''
		COMMENT 'Title of the document',
	`alias`
		VARCHAR(255)
		CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
		DEFAULT ''
		COMMENT 'Alias of the document used in sef mode',
	`mime`
		VARCHAR(255)
		NOT NULL
		DEFAULT ''
		COMMENT 'Classification of types of files on the Internet',
	`featured`
		TINYINT(3)
		NOT NULL
		DEFAULT '0'
		COMMENT 'Boolean indicating that the document is highlighted',
	`ordering`
		INTEGER UNSIGNED
		NOT NULL
		DEFAULT '0'
		COMMENT 'The serial number of a document',
	`published`
		INTEGER
		NOT NULL
		DEFAULT '0'
		COMMENT 'Integer indicating document status',
	`publish_up`
		DATE
		NOT NULL
		DEFAULT '0000-00-00 00:00:00'
		COMMENT 'Document publication starting date',
	`publish_down`
		DATE
		NOT NULL
		DEFAULT '0000-00-00 00:00:00'
		COMMENT 'Document publication ending date',
	`access`
		INTEGER UNSIGNED
		NOT NULL
		DEFAULT '0'
		COMMENT 'Describe the minimum level of privileges for access to the document',
	`hits`
		INTEGER UNSIGNED
		NOT NULL
		DEFAULT '0'
		COMMENT 'Symbolize a number of clics on the link to download the document',
	`language`
		CHAR(7)
		NOT NULL
		DEFAULT ''
		COMMENT 'Allow the encoding of the document language',
	`checked_out_time`
		DATE
		NOT NULL
		DEFAULT 0
		DEFAULT '0000-00-00 00:00:00'
		COMMENT 'Document edition starting date',
	`checked_out`
		INTEGER UNSIGNED
		NOT NULL
		DEFAULT '0'
		COMMENT 'FK to the #__users table representing the user who is currently editing the document',
	`created`
		DATE
		NOT NULL
		DEFAULT '0000-00-00 00:00:00'
		COMMENT 'Document creation date',
	`created_by`
		INTEGER UNSIGNED
		NOT NULL
		DEFAULT '0'
		COMMENT 'FK to the #__users table identifying the Joomla user who created the document',
	`modified`
		DATE
		NOT NULL
		DEFAULT '0000-00-00 00:00:00'
		COMMENT 'Document modification date',
	`modified_by`
		INTEGER UNSIGNED
		NOT NULL
		DEFAULT '0'
		COMMENT 'FK to the #__users table identifying the Joomla user who modified the document',
	PRIMARY KEY `idx_id` (`id`),
	UNIQUE `idx_alias` (`alias`),
	KEY `idx_mime` (`mime`),
	KEY `idx_featured` (`featured`),
	KEY `idx_ordering` (`ordering`),
	KEY `idx_published` (`published`),
	KEY `idx_publish_up` (`publish_up`),
	KEY `idx_publish_down` (`publish_down`),
	KEY `idx_access` (`access`),
	KEY `idx_hits` (`hits`),
	KEY `idx_language` (`language`),
	KEY `idx_created_by` (`created_by`),
	KEY `idx_created` (`created`),
	KEY `idx_modified_by` (`modified_by`),
	KEY `idx_modified` (`modified`)
) DEFAULT CHARSET=utf8;

-- TODO: add index on author, description
CREATE TABLE IF NOT EXISTS `#__document_version` (
	`id`
		INTEGER
		NOT NULL
		AUTO_INCREMENT
		COMMENT 'PK of this table',
	`document_id`
		INTEGER UNSIGNED
		NOT NULL
		COMMENT 'FK to the #__document table',
	`number`
		INTEGER UNSIGNED
		NOT NULL
		COMMENT 'Version number',
	`author`
		VARCHAR(255)
		NOT NULL
		DEFAULT ''
		COMMENT 'Author of the document ',
	`description`
		TEXT
		NOT NULL
		DEFAULT ''
		COMMENT 'Description of the document',
	`params`
		TEXT
		NOT NULL
		DEFAULT ''
		COMMENT 'String that can store additional settings',
	`filename`
		VARCHAR(255)
		NOT NULL
		DEFAULT ''
		COMMENT 'Document filename',
	`created`
		DATE
		NOT NULL
		DEFAULT '0000-00-00 00:00:00'
		COMMENT 'Document creation date',
	`created_by`
		INTEGER UNSIGNED
		NOT NULL
		DEFAULT '0'
		COMMENT 'FK to the #__users table identifying the Joomla user who created the document',
	`modified`
		DATE
		NOT NULL
		DEFAULT '0000-00-00 00:00:00'
		COMMENT 'Document modification date',
	`modified_by`
		INTEGER UNSIGNED
		NOT NULL
		DEFAULT '0'
		COMMENT 'FK to the #__users table identifying the Joomla user who modified the document',
	PRIMARY KEY (`id`),
	UNIQUE `idx_id_version` (`document_id`, `version`),
	KEY `idx_created_by` (`created_by`),
	KEY `idx_modified_by` (`modified_by`),
	KEY `idx_created` (`created`),
	KEY `idx_modified` (`modified`)
) DEFAULT CHARSET=utf8;

-- TODO: Add an index on keywords
CREATE TABLE IF NOT EXISTS `#__document_keyword` (
	`id`
		INTEGER
		NOT NULL
		AUTO_INCREMENT
		COMMENT 'PK of this table',
	`title`
		VARCHAR(255)
		NOT NULL
		DEFAULT ''
		COMMENT 'Keyword',
	`alias`
		VARCHAR(255)
		CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
		DEFAULT ''
		COMMENT 'Alias of the keyword used in sef mode',
	`language`
		CHAR(7)
		NOT NULL
		DEFAULT ''
		COMMENT 'Allow the encoding of the keyword language',
	PRIMARY KEY  (`id`),
	UNIQUE `idx_title` (`title`),
	UNIQUE `idx_alias` (`alias`),
	KEY `idx_language` (`language`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__document_keyword_map` (
	`document_id`
		INTEGER UNSIGNED
		NOT NULL
		COMMENT 'FK to the #__document table',
	`version`
		INTEGER UNSIGNED
		NOT NULL
		COMMENT 'Version number',
	`keyword_id`
		INTEGER UNSIGNED
		NOT NULL
		COMMENT 'FK to the #__document_keyword table',
	PRIMARY KEY `idx_document_keyword` (`document_id`, `version`, `keyword_id`),
	UNIQUE `idx_keyword_document`  (`keyword_id`, `document_id`, `version`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__document_category_map` (
	`document_id`
		INTEGER UNSIGNED
		NOT NULL
		COMMENT 'FK to the #__document table',
	`category_id`
		INTEGER UNSIGNED
		NOT NULL
		COMMENT 'FK to the #__categories table',
	PRIMARY KEY `idx_document_category` (`document_id`, `category_id`),
	UNIQUE `idx_category_document`  (`category_id`, `document_id`)
) DEFAULT CHARSET=utf8;
