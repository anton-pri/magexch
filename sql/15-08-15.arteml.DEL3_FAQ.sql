-- FAQ addon
DELETE FROM cw_addons WHERE addon='faq';
DELETE FROM cw_languages WHERE name IN ('addon_name_faq','eml_faq_question','eml_faq_tell_a_friend','lbl_add_faq_question','lbl_ars_faqs','lbl_faq','lbl_faq_argument','lbl_faq_ask_a_question','lbl_faq_ask_first','lbl_faq_categories','lbl_faq_category','lbl_faq_close_question','lbl_faq_content','lbl_faq_find','lbl_faq_long','lbl_faq_main_page','lbl_faq_question','lbl_faq_questions','lbl_faq_rubrik','lbl_faq_section_customer','lbl_faq_section_header','lbl_faq_section_salesman','lbl_faq_section_warehouse','lbl_help_section_faq','lbl_no_faq_for_membership','lbl_no_faq_for_product','lbl_section_faq','txt_faq','txt_faq_comments_required','txt_faq_comment_saved','txt_faq_fields_required','txt_faq_incorrect_email','txt_faq_statement','txt_faq_vote_error','txt_faq_vote_placed');

DROP TABLE IF EXISTS cw_faq_comments,cw_faq_data,cw_faq_data_lng,cw_faq_files,cw_faq_questions,cw_faq_questions_lng,cw_faq_rubrik,cw_faq_rubrik_count,cw_faq_rubrik_parents,cw_faq_visits,cw_faq_voting;

-- Price list
DROP TABLE IF EXISTS cw_price_lists,cw_price_lists_def,cw_price_lists_def_categories,cw_price_lists_def_manufacturers;


-- Backup
/*

INSERT INTO `cw_languages` VALUES ('en','addon_name_faq','FAQs','','Addons'),('EN','eml_faq_question','New question has been placed','','E-Mail'),('EN','eml_faq_tell_a_friend','FAQ link','','E-Mail'),('EN','lbl_add_faq_question','Add FAQ question','','Labels'),('EN','lbl_ars_faqs','Eshop FAQs','','Labels'),('EN','lbl_faq','FAQ','','Labels'),('EN','lbl_faq_argument','FAQ Argument','','Labels'),('EN','lbl_faq_ask_a_question','Ask a New Question','','Labels'),('EN','lbl_faq_ask_first','Do you want to be the first to ask something about it?','','Labels'),('EN','lbl_faq_categories','FAQ categories','','Labels'),('EN','lbl_faq_category','FAQ Category','','Labels'),('EN','lbl_faq_close_question','Close this question','','Labels'),('EN','lbl_faq_content','FAQ content','','Labels'),('EN','lbl_faq_find','You\'ll find the record on the following address','','Labels'),('EN','lbl_faq_long','FAQ (Frequently Asked Questions)','','Labels'),('EN','lbl_faq_main_page','FAQ Main Page','','Labels'),('EN','lbl_faq_question','FAQ question','','Labels'),('EN','lbl_faq_questions','FAQ questions','','Labels'),('EN','lbl_faq_rubrik','FAQ category','','Labels'),('EN','lbl_faq_section_customer','Customer Section','','Labels'),('EN','lbl_faq_section_header','Frequently Asked Questions<br/>Support Area','','Labels'),('EN','lbl_faq_section_salesman','Salemanagers Section','','Labels'),('EN','lbl_faq_section_warehouse','Warehouses Section','','Labels'),('EN','lbl_help_section_faq','FAQ','','Labels'),('EN','lbl_no_faq_for_membership','No any FAQ is available at this moment.','','Labels'),('EN','lbl_no_faq_for_product','No any FAQ is available for this product.','','Labels'),('EN','lbl_section_faq','FAQ','','Labels'),('EN','txt_faq','FAQ goes here','','Text'),('EN','txt_faq_comments_required','Required fields are <b>your name</b>, <b>your email address</b> and <b>your comments</b>!<br><br>','','Text'),('EN','txt_faq_comment_saved','Thanks a lot for your comments!','','Text'),('EN','txt_faq_fields_required','Required fields are <b>your name</b>, <b>your email address</b>.','','Text'),('EN','txt_faq_incorrect_email','Please correct your e-mail address.','','Text'),('en','txt_faq_statement','Place FAQ for your customers here. To change this text, please edit the language variable \'txt_faq_statement\' from \'Languages\' menu in the admin area.','','Text'),('EN','txt_faq_vote_error','You already vote for this question!','','Text'),('EN','txt_faq_vote_placed','FAQ vote has been placed','','Text');

CREATE TABLE IF NOT EXISTS `cw_faq_comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL DEFAULT '0',
  `email` varchar(255) NOT NULL DEFAULT '',
  `comment` mediumtext NOT NULL,
  `date` int(11) NOT NULL DEFAULT '0',
  `helped` mediumtext NOT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cw_faq_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` varchar(5) DEFAULT '',
  `active` int(1) NOT NULL DEFAULT '0',
  `rubrik` mediumtext NOT NULL,
  `keywords` mediumtext NOT NULL,
  `thema` mediumtext NOT NULL,
  `content` mediumtext NOT NULL,
  `author` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `comment` enum('y','n') NOT NULL DEFAULT 'y',
  `date` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `active` (`active`),
  FULLTEXT KEY `rubrik` (`rubrik`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cw_faq_data_lng` (
  `id` int(11) NOT NULL DEFAULT '0',
  `code` char(2) NOT NULL DEFAULT '',
  `thema` mediumtext NOT NULL,
  `content` mediumtext NOT NULL,
  PRIMARY KEY (`id`,`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cw_faq_files` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `by_customer_id` int(11) NOT NULL DEFAULT '0',
  `file_path` varchar(255) NOT NULL DEFAULT '',
  `filename` varchar(255) NOT NULL DEFAULT '',
  `descr` mediumtext NOT NULL,
  `date` int(11) NOT NULL DEFAULT '0',
  `orderby` int(11) NOT NULL DEFAULT '0',
  `md5` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`file_id`),
  KEY `image_path` (`file_path`),
  KEY `id` (`customer_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cw_faq_questions` (
  `question_id` int(11) NOT NULL AUTO_INCREMENT,
  `active` int(1) NOT NULL DEFAULT '0',
  `rubrik_id` int(11) NOT NULL DEFAULT '0',
  `keywords` mediumtext NOT NULL,
  `thema` mediumtext NOT NULL,
  `content` mediumtext NOT NULL,
  `author` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `comment` int(1) NOT NULL DEFAULT '0',
  `date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`question_id`),
  KEY `rubrik_id` (`rubrik_id`),
  FULLTEXT KEY `keywords` (`keywords`,`thema`,`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `cw_faq_questions_lng` (
  `question_id` int(11) NOT NULL DEFAULT '0',
  `code` char(2) NOT NULL DEFAULT '',
  `thema` mediumtext NOT NULL,
  `content` mediumtext NOT NULL,
  PRIMARY KEY (`question_id`,`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cw_faq_rubrik` (
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `rubrik_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `date` int(11) NOT NULL DEFAULT '0',
  `ars_id` int(11) NOT NULL DEFAULT '0',
  `ars_type` varchar(8) NOT NULL DEFAULT '',
  PRIMARY KEY (`rubrik_id`),
  KEY `aa` (`ars_id`,`ars_type`),
  KEY `rp` (`rubrik_id`,`parent_id`),
  KEY `parent_id` (`parent_id`),
  KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cw_faq_rubrik_count` (
  `rubrik_id` int(11) NOT NULL DEFAULT '0',
  `subrubrik_count` int(11) NOT NULL DEFAULT '0',
  `questions_count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rubrik_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cw_faq_rubrik_parents` (
  `rubrik_id` int(11) NOT NULL DEFAULT '0',
  `parent_rubrik_id` int(11) NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rubrik_id`,`parent_rubrik_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cw_faq_visits` (
  `question_id` int(11) NOT NULL DEFAULT '0',
  `visits` int(11) NOT NULL DEFAULT '0',
  `last_visit` int(15) NOT NULL DEFAULT '0',
  PRIMARY KEY (`question_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cw_faq_voting` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL DEFAULT '0',
  `vote` int(11) unsigned NOT NULL DEFAULT '0',
  `date` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
*/

/*
CREATE TABLE IF NOT EXISTS `cw_price_lists` (
  `price_list_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL DEFAULT '',
  `profit` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `discount` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `discount_type` int(2) NOT NULL DEFAULT '0',
  `orderby` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`price_list_id`)
) ENGINE=MyISAM;
CREATE TABLE IF NOT EXISTS `cw_price_lists_def` (
  `price_list_def_id` int(11) NOT NULL AUTO_INCREMENT,
  `price_list_id` int(11) NOT NULL DEFAULT '0',
  `profit` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `orderby` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`price_list_def_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `cw_price_lists_def_categories` (
  `price_list_def_id` int(11) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`price_list_def_id`,`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `cw_price_lists_def_manufacturers` (
  `price_list_def_id` int(11) NOT NULL DEFAULT '0',
  `manufacturer_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`price_list_def_id`,`manufacturer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/
