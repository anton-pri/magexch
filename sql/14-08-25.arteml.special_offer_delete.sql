DROP TABLE cw_offers, cw_offers_lng, cw_offer_conditions, cw_offer_condition_params, cw_offer_bonuses, cw_offer_bonus_params, cw_customer_bonuses, cw_condition_memberships, cw_bonus_memberships;

select @cid:=config_category_id from cw_config_categories where category='special_offers';
delete from cw_config_categories where category='special_offers';
delete from cw_config where config_category_id=@cid;

delete from cw_addons where addon='special_offers';

delete from cw_navigation_targets where addon='special_offers';
delete from cw_navigation_tabs where link='index.php?target=offers';
delete from cw_breadcrumbs where link like '%target=offers%';

delete from cw_languages where name IN ('lbl_special_offer','lbl_special_offers','config_special_offers','addon_name_special_offers','addon_descr_special_offers','lbl_special_offer_bonus_B','lbl_special_offer_bonus_D','lbl_special_offer_bonus_M','lbl_special_offer_bonus_N','lbl_special_offer_bonus_S','lbl_special_offer_condition_B','lbl_special_offer_condition_D','lbl_special_offer_condition_M','lbl_special_offer_condition_N','lbl_special_offer_condition_S','lbl_special_offer_condition_T','option_title_special_offers','txt_special_offers_descr');
delete from cw_languages_alt where name IN ('lbl_special_offer','lbl_special_offers','config_special_offers','addon_name_special_offers','addon_descr_special_offers','lbl_special_offer_bonus_B','lbl_special_offer_bonus_D','lbl_special_offer_bonus_M','lbl_special_offer_bonus_N','lbl_special_offer_bonus_S','lbl_special_offer_condition_B','lbl_special_offer_condition_D','lbl_special_offer_condition_M','lbl_special_offer_condition_N','lbl_special_offer_condition_S','lbl_special_offer_condition_T','option_title_special_offers','txt_special_offers_descr');
delete from cw_langvars_statistics where name IN ('lbl_special_offer','lbl_special_offers','config_special_offers','addon_name_special_offers','addon_descr_special_offers','lbl_special_offer_bonus_B','lbl_special_offer_bonus_D','lbl_special_offer_bonus_M','lbl_special_offer_bonus_N','lbl_special_offer_bonus_S','lbl_special_offer_condition_B','lbl_special_offer_condition_D','lbl_special_offer_condition_M','lbl_special_offer_condition_N','lbl_special_offer_condition_S','lbl_special_offer_condition_T','option_title_special_offers','txt_special_offers_descr');

/* Config backup
INSERT INTO `cw_config` (`name`, `comment`, `value`, `config_category_id`, `orderby`, `type`, `defvalue`, `variants`) VALUES
('offers_bp_rate', 'Bonus points to Gift Certificate conversion rate', '0.1', 42, 10, 'numeric', '', ''),
('offers_bp_min', 'Minimum allowed amount of bonus points to convert', '100', 42, 15, 'numeric', '', ''),
('offers_per_row', 'Display offers list in multiple columns (1-3)', '3', 42, 20, 'numeric', '', ''),
('offers_list_limit', 'Maximum number of offers in the list (at category and product pages)', '3', 42, 25, 'numeric', '', '');
*/

/* Tables schema backup
--
-- Структура таблицы `cw_bonus_memberships`
--

CREATE TABLE IF NOT EXISTS `cw_bonus_memberships` (
  `bonus_id` int(11) NOT NULL DEFAULT '0',
  `membership_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`bonus_id`,`membership_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_condition_memberships`
--

CREATE TABLE IF NOT EXISTS `cw_condition_memberships` (
  `condition_id` int(11) NOT NULL DEFAULT '0',
  `membership_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`condition_id`,`membership_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_customer_bonuses`
--

CREATE TABLE IF NOT EXISTS `cw_customer_bonuses` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `points` int(11) NOT NULL DEFAULT '0',
  `memberships` mediumtext NOT NULL,
  PRIMARY KEY (`customer_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=159 ;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_offers`
--

CREATE TABLE IF NOT EXISTS `cw_offers` (
  `offer_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `from_date` int(11) NOT NULL DEFAULT '0',
  `to_date` int(11) NOT NULL DEFAULT '0',
  `avail` int(1) NOT NULL DEFAULT '0',
  `show_short_promo` int(1) NOT NULL DEFAULT '0',
  `promo_short` mediumtext NOT NULL,
  `promo_long` mediumtext NOT NULL,
  PRIMARY KEY (`offer_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=714 ;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_offers_lng`
--

CREATE TABLE IF NOT EXISTS `cw_offers_lng` (
  `offer_id` int(11) NOT NULL DEFAULT '0',
  `code` varchar(2) NOT NULL DEFAULT '',
  `promo_short` mediumtext,
  `promo_long` mediumtext,
  PRIMARY KEY (`offer_id`,`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_offer_bonuses`
--

CREATE TABLE IF NOT EXISTS `cw_offer_bonuses` (
  `bonus_id` int(11) NOT NULL AUTO_INCREMENT,
  `offer_id` int(11) NOT NULL DEFAULT '0',
  `bonus_type` char(1) NOT NULL DEFAULT '',
  `amount_type` char(1) NOT NULL DEFAULT '',
  `amount_min` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `amount_max` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `bonus_data` mediumtext,
  `warehouse_customer_id` int(11) NOT NULL DEFAULT '0',
  `avail` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`bonus_id`),
  UNIQUE KEY `b_type` (`offer_id`,`bonus_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2513 ;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_offer_bonus_params`
--

CREATE TABLE IF NOT EXISTS `cw_offer_bonus_params` (
  `param_id` int(11) NOT NULL AUTO_INCREMENT,
  `bonus_id` int(11) NOT NULL DEFAULT '0',
  `param_type` char(1) NOT NULL DEFAULT '',
  `param_key` int(11) NOT NULL DEFAULT '0',
  `param_arg` char(1) NOT NULL DEFAULT '',
  `param_qnty` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`param_id`),
  KEY `bonus_id_type` (`bonus_id`,`param_type`),
  KEY `bonus_id` (`bonus_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=663 ;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_offer_conditions`
--

CREATE TABLE IF NOT EXISTS `cw_offer_conditions` (
  `condition_id` int(11) NOT NULL AUTO_INCREMENT,
  `offer_id` int(11) NOT NULL DEFAULT '0',
  `condition_type` char(1) NOT NULL DEFAULT '',
  `amount_type` char(1) NOT NULL DEFAULT '',
  `amount_min` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `amount_max` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `condition_data` mediumtext,
  `avail` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`condition_id`),
  UNIQUE KEY `c_type` (`offer_id`,`condition_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2943 ;

-- --------------------------------------------------------

--
-- Структура таблицы `cw_offer_condition_params`
--

CREATE TABLE IF NOT EXISTS `cw_offer_condition_params` (
  `param_id` int(11) NOT NULL AUTO_INCREMENT,
  `condition_id` int(11) NOT NULL DEFAULT '0',
  `param_type` int(2) NOT NULL DEFAULT '0',
  `param_key` int(11) NOT NULL DEFAULT '0',
  `param_arg` char(1) NOT NULL DEFAULT '',
  `param_qnty` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`param_id`),
  KEY `args1` (`param_type`,`param_key`,`param_arg`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17253 ;
*/
