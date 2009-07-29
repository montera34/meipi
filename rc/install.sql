-- --------------------------------------------------------

-- 
-- Table structure for table `meipi_category`
-- 

CREATE TABLE `meipi_category` (
  `id_category` int(10) unsigned NOT NULL auto_increment,
  `category_name` varchar(30) NOT NULL default '',
  `category_desc` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_category`),
  UNIQUE KEY `category_name` (`category_name`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `meipi_comment`
-- 

CREATE TABLE `meipi_comment` (
  `id_comment` int(10) unsigned NOT NULL auto_increment,
  `subject` varchar(255) NOT NULL default '',
  `text` text NOT NULL,
  `id_user` int(10) unsigned NOT NULL default '0',
  `id_entry` int(10) unsigned NOT NULL default '0',
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id_comment`),
  KEY `id_entry` (`id_entry`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `meipi_content`
-- 

CREATE TABLE `meipi_content` (
  `id_content` int(10) unsigned NOT NULL auto_increment,
  `file` varchar(50) NOT NULL default '',
  `content_name` varchar(30) NOT NULL default '',
  `id_entry` int(10) unsigned NOT NULL default '0',
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `type` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_content`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `meipi_entry`
-- 

CREATE TABLE `meipi_entry` (
  `id_entry` int(10) unsigned NOT NULL auto_increment,
  `address` varchar(255) NOT NULL default '',
  `longitude` float NOT NULL default '0',
  `latitude` float NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `text` text NOT NULL,
  `id_user` int(10) unsigned NOT NULL default '0',
  `id_category` int(10) unsigned NOT NULL default '0',
  `entry_date` date NOT NULL default '0000-00-00',
  `date` datetime default NULL,
  `ranking` float NOT NULL default '0',
  `votes` int(10) unsigned NOT NULL default '0',
  `url` varchar(255) default NULL,
  `comments` int(10) unsigned NOT NULL default '0',
  `edited` tinyint(3) unsigned NOT NULL default '0',
  `last_edited` datetime NOT NULL,
  `last_editor` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_entry`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `meipi_entry_tag`
-- 

CREATE TABLE `meipi_entry_tag` (
  `id_entry_tag` int(10) unsigned NOT NULL auto_increment,
  `id_entry` int(10) unsigned NOT NULL default '0',
  `id_tag` int(10) unsigned NOT NULL default '0',
  `position` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_entry_tag`),
  KEY `id_entry` (`id_entry`,`id_tag`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `meipi_extra`
-- 

CREATE TABLE `meipi_extra` (
  `id_entry` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id_entry`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `meipi_mosaic`
-- 

CREATE TABLE `meipi_mosaic` (
  `id_mosaic` int(10) unsigned NOT NULL auto_increment,
  `id_user` int(10) unsigned NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `date_created` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `date_saved` timestamp NOT NULL default '0000-00-00 00:00:00',
  `rows` tinyint(3) unsigned NOT NULL default '0',
  `cols` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_mosaic`),
  UNIQUE KEY `id_user` (`id_user`,`name`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `meipi_mosaic_item`
-- 

CREATE TABLE `meipi_mosaic_item` (
  `id_mosaic_item` int(10) unsigned NOT NULL auto_increment,
  `id_mosaic` int(10) unsigned NOT NULL default '0',
  `id_content` int(10) unsigned NOT NULL default '0',
  `x` tinyint(3) unsigned NOT NULL default '0',
  `y` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_mosaic_item`),
  UNIQUE KEY `id_content` (`id_mosaic`,`x`,`y`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `meipi_selected_item`
-- 

CREATE TABLE `meipi_selected_item` (
  `id_user` int(10) unsigned NOT NULL default '0',
  `id_content` int(10) unsigned NOT NULL default '0',
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id_user`,`id_content`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `meipi_tag`
-- 

CREATE TABLE `meipi_tag` (
  `id_tag` int(10) unsigned NOT NULL auto_increment,
  `tag_name` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`id_tag`),
  UNIQUE KEY `tag_name` (`tag_name`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `meipi_vote`
-- 

CREATE TABLE `meipi_vote` (
  `id_vote` int(10) unsigned NOT NULL auto_increment,
  `id_user` int(10) unsigned NOT NULL default '0',
  `id_entry` int(10) unsigned NOT NULL default '0',
  `vote` float NOT NULL default '0',
  PRIMARY KEY  (`id_user`,`id_entry`),
  UNIQUE KEY `id_vote` (`id_vote`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `meipi`
-- 

CREATE TABLE `meipi` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `code` varchar(50) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `type` smallint(5) unsigned NOT NULL default '0',
  `id_creator` int(10) unsigned NOT NULL default '0',
  `meipimatic_code` varchar(10) NOT NULL default '',
  `date_created` timestamp NULL default NULL,
  `aliascode` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `meipi_config`
-- 

CREATE TABLE `meipi_config` (
  `id_meipi` varchar(20) NOT NULL default '',
  `name` varchar(30) NOT NULL default '',
  `value` varchar(255) NOT NULL,
  PRIMARY KEY  (`id_meipi`,`name`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `meipi_description`
-- 

CREATE TABLE `meipi_description` (
  `id_meipi` varchar(20) NOT NULL default '',
  `long_description` text NOT NULL,
  PRIMARY KEY  (`id_meipi`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `meipi_global_stats`
-- 

CREATE TABLE `meipi_global_stats` (
  `meipis` int(10) unsigned NOT NULL default '0',
  `entries` int(10) unsigned NOT NULL default '0',
  `users` int(10) unsigned NOT NULL default '0'
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `meipi_messages`
-- 

CREATE TABLE `meipi_messages` (
  `id_message` bigint(20) unsigned NOT NULL auto_increment,
  `from` int(10) unsigned NOT NULL,
  `to` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `read` tinyint(3) unsigned NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY  (`id_message`),
  KEY `to` (`to`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `meipi_params`
-- 

CREATE TABLE `meipi_params` (
  `id_meipi` varchar(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `description` varchar(50) default '',
  `type` varchar(20) NOT NULL,
  `value` varchar(255) NOT NULL,
  `config` varchar(255) NOT NULL,
  PRIMARY KEY  (`id_meipi`,`name`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `meipi_reset_password`
-- 

CREATE TABLE `meipi_reset_password` (
  `id_user` int(10) unsigned NOT NULL,
  `code` varchar(16) NOT NULL,
  `valid_until` datetime NOT NULL
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `user`
-- 

CREATE TABLE `meipi_user` (
  `id_user` int(10) unsigned NOT NULL auto_increment,
  `login` varchar(30) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `web` varchar(150) NOT NULL default '',
  `mail` varchar(50) default NULL,
  `date` datetime default NULL,
  `mail_subscription` int(1) unsigned NOT NULL default '1',
  `fullname` varchar(50) NOT NULL,
  `about` varchar(255) NOT NULL,
  `image` varchar(50) default '',
  `language` varchar(2) NOT NULL default '',
  PRIMARY KEY  (`id_user`),
  UNIQUE KEY `login` (`login`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `permission`
-- 

CREATE TABLE `meipi_permission` (
  `id_user` int(10) unsigned NOT NULL default '0',
  `type` int(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_user`,`type`)
) ENGINE=MyISAM;

-- --------------------------------------------------------

-- 
-- Default categories
-- 

insert into meipi_category(category_name, category_desc) values("category1", "category 1");
insert into meipi_category(category_name, category_desc) values("category2", "category 2");
insert into meipi_category(category_name, category_desc) values("category3", "category 3");
insert into meipi_category(category_name, category_desc) values("category4", "category 4");

