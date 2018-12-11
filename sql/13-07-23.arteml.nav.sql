-- phpMyAdmin SQL Dump
-- version 3.5.8.1deb1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Июл 23 2013 г., 13:45
-- Версия сервера: 5.5.31-0ubuntu0.13.04.1
-- Версия PHP: 5.4.9-4ubuntu2.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `cart`
--

-- --------------------------------------------------------

--
-- Структура таблицы `cw_navigation_sections`
--

DROP TABLE IF EXISTS `cw_navigation_sections`;
CREATE TABLE IF NOT EXISTS `cw_navigation_sections` (
  `section_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(32) NOT NULL DEFAULT '',
  `link` text NOT NULL,
  `addon` varchar(255) NOT NULL DEFAULT '',
  `access_level` varchar(16) NOT NULL DEFAULT '',
  `area` char(1) NOT NULL DEFAULT '',
  `main` char(1) NOT NULL DEFAULT 'Y',
  `orderby` int(11) NOT NULL DEFAULT '0',
  `skins_subdir` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`section_id`),
  KEY `area` (`area`),
  KEY `sa` (`section_id`,`area`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2128 ;

--
-- Дамп данных таблицы `cw_navigation_sections`
--

INSERT INTO `cw_navigation_sections` (`section_id`, `title`, `link`, `addon`, `access_level`, `area`, `main`, `orderby`, `skins_subdir`) VALUES
(62, 'lbl_section_customers', 'index.php?target=user_C', '', '08', 'A', 'Y', 10, 'users'),
(65, 'lbl_section_sales_managers', 'index.php?target=user_B', 'Salesman', '11', 'A', 'Y', 35, 'sales_manager'),
(66, 'lbl_products', 'index.php?target=products', '', '12', 'A', 'Y', 40, 'products'),
(71, 'lbl_section_orders', 'index.php?target=docs_O', '', '18', 'A', 'Y', 90, 'orders'),
(73, 'lbl_section_invoices', 'index.php?target=docs_I', '', '20', 'A', 'Y', 110, 'orders'),
(114, 'lbl_section_webmaster', 'include.php?target=custom_cc', '', '2905', 'A', 'N', 0, 'webmaster'),
(78, 'lbl_companies', '', '', '', 'A', 'N', 0, 'companies'),
(79, 'lbl_profile_options', 'index.php?target=user_profiles', '', '2502', 'A', 'N', 0, 'profile_options'),
(80, 'lbl_news', 'index.php?target=news', '', '26', 'A', 'N', 0, 'news'),
(81, 'lbl_customer_side_news', 'index.php?target=news_c', '', '2601', 'A', 'N', 0, 'news'),
(82, 'lbl_section_anagraphic', 'index.php?target=products', '', '11', 'P', 'Y', 5, 'products'),
(83, 'lbl_shipping', 'index.php?target=shipping', '', '2504', 'A', 'N', 0, 'shipping-system'),
(84, 'lbl_languages', 'index.php?target=languages', '', '2904', 'A', 'N', 0, 'languages'),
(85, 'lbl_payment_section', '', '', '2503', 'A', 'N', 0, 'payment-system'),
(87, 'lbl_countries', 'index.php?target=countries', '', '2507', 'A', 'N', 0, 'map'),
(89, 'lbl_section_admins', 'index.php?target=user_A', '', '02', 'A', 'Y', 0, 'users'),
(90, 'lbl_section_warehouses', 'index.php?target=user_P', 'Warehouse', '030000000', 'A', 'Y', 0, 'users'),
(91, 'lbl_section_memberships', 'index.php?target=memberships', '', '2508', 'A', 'N', 0, 'memberships'),
(93, 'lbl_import_data', 'index.php?target=import', '', '13', 'A', 'Y', 45, 'import_export'),
(94, 'lbl_section_taxes', 'index.php?target=taxes', '', '2505', 'A', 'N', 0, 'taxes'),
(95, 'lbl_section_addons', 'index.php?target=addons', '', '2501', 'A', 'N', 0, 'configuration'),
(96, 'lbl_settings', 'index.php?target=settings', '', '2500', 'A', 'N', 0, 'settings'),
(97, 'lbl_section_seo', 'index.php?target=meta_tags', '', '28', 'A', 'N', 0, 'seo'),
(98, 'lbl_section_orders', 'index.php?target=docs_O', '', '12', 'P', 'Y', 10, 'orders'),
(99, 'lbl_section_invoices', 'index.php?target=docs_I', '', '13', 'P', 'Y', 20, 'orders'),
(100, 'lbl_section_shipment_docs', 'index.php?target=docs_S', '', '14', 'P', 'Y', 30, 'orders'),
(101, 'lbl_shipping_rates', 'index.php?target=shipping_rates', '', '15', 'P', 'N', 0, 'shipping'),
(102, 'lbl_users_C', 'index.php?target=user_customers', '', '', 'B', 'Y', 10, 'users'),
(103, 'lbl_docs_info_O', 'index.php?target=docs_O', '', '', 'B', 'Y', 20, 'orders'),
(104, 'lbl_docs_info_I', 'index.php?target=docs_I', '', '', 'B', 'Y', 30, 'orders'),
(105, 'lbl_docs_info_S', 'index.php?target=docs_S', '', '', 'B', 'Y', 40, 'orders'),
(106, 'lbl_section_banners', 'index.php?target=products', '', '', 'B', 'Y', 50, 'banners'),
(109, 'lbl_docs_info_B', 'index.php?target=docs_B', '', '', 'B', 'Y', 15, 'orders'),
(111, 'lbl_section_user_pos', 'index.php?target=user_G', 'pos', '06', 'A', 'Y', 0, 'users'),
(112, 'lbl_section_cash_selling', 'index.php?target=docs_G', 'pos', '07', 'A', 'Y', 0, 'orders'),
(113, 'lbl_section_movements', 'index.php?target=movements', '', '10', 'P', 'Y', 0, 'accounting'),
(115, 'lbl_section_surveys', 'index.php?target=surveys', 'survey', '31', 'A', 'Y', 0, 'surveys'),
(116, 'lbl_wishlist', 'index.php?target=gifts', 'estore_gift', '', 'C', 'N', 0, 'gifts'),
(117, 'lbl_section_warehouse_sharing', 'index.php?target=ws', 'WarehouseSharing', '2401', 'A', 'N', 0, 'warehouse_sharing'),
(121, 'lbl_static_pages', 'index.php?target=pages', '', '2906', 'A', 'N', 0, 'pages'),
(122, 'lbl_domains', 'index.php?target=domains', 'multi_domains', '32', 'A', 'Y', 100, ''),
(2122, 'lbl_ab_ad_banners', 'index.php?target=ad_banners', 'ad_banners', '', 'A', 'Y', 0, 'ad_banners'),
(2124, 'lbl_sitemap_xml', 'index.php?target=sitemap_xml', 'sitemap_xml', '', 'A', 'Y', 0, 'sitemap_xml'),
(2125, 'lbl_ps_offers', 'index.php?target=promosuite', 'promotion_suite', '', 'A', 'Y', 0, ''),
(2127, 'lbl_export_data', 'index.php?target=import&mode=expdata', '', '13', 'A', 'Y', 46, 'import_export');

-- --------------------------------------------------------

--
-- Структура таблицы `cw_navigation_settings`
--

DROP TABLE IF EXISTS `cw_navigation_settings`;
CREATE TABLE IF NOT EXISTS `cw_navigation_settings` (
  `target_id` int(11) NOT NULL DEFAULT '0',
  `config_category_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`target_id`,`config_category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `cw_navigation_settings`
--

INSERT INTO `cw_navigation_settings` (`target_id`, `config_category_id`) VALUES
(88, 50),
(89, 30),
(89, 50),
(91, 38),
(91, 50),
(112, 77),
(113, 77),
(132, 0),
(133, 0),
(134, 0),
(141, 38),
(141, 77),
(142, 38),
(142, 77),
(143, 38),
(143, 50),
(143, 68),
(147, 61),
(147, 77),
(148, 77),
(149, 39),
(149, 49),
(157, 77),
(158, 77),
(160, 50),
(160, 71),
(163, 50),
(163, 71),
(167, 6),
(173, 50),
(173, 68),
(192, 43),
(194, 77),
(195, 77),
(203, 47),
(207, 37),
(265, 0),
(276, 77),
(277, 77),
(284, 72),
(286, 72),
(289, 72),
(290, 77),
(291, 77),
(293, 77),
(294, 77),
(2351, 80);

-- --------------------------------------------------------

--
-- Структура таблицы `cw_navigation_tabs`
--

DROP TABLE IF EXISTS `cw_navigation_tabs`;
CREATE TABLE IF NOT EXISTS `cw_navigation_tabs` (
  `tab_id` int(11) NOT NULL AUTO_INCREMENT,
  `access_level` varchar(16) NOT NULL DEFAULT '',
  `title` varchar(64) NOT NULL DEFAULT '',
  `link` text NOT NULL,
  `orderby` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tab_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2303 ;

--
-- Дамп данных таблицы `cw_navigation_tabs`
--

INSERT INTO `cw_navigation_tabs` (`tab_id`, `access_level`, `title`, `link`, `orderby`) VALUES
(1003, '', 'lbl_search_users', 'index.php?target=users', 0),
(1004, '', 'lbl_create_admin_profile', 'index.php?target=user_add&usertype=A', 0),
(1005, '', 'lbl_create_warehouse_profile', 'index.php?target=user_add&usertype=P', 0),
(1006, '__0801', 'lbl_user_create_C', 'index.php?target=user_C&mode=add', 30),
(1007, '', 'lbl_create_salesman_profile', 'index.php?target=user_add&usertype=B', 0),
(1008, '', 'lbl_membership_levels', 'index.php?target=memberships', 0),
(1009, '', 'lbl_titles', 'index.php?target=titles', 0),
(1010, '', 'lbl_survey_surveys', 'index.php?target=surveys', 0),
(1011, '1101', 'lbl_affiliate_plans', 'index.php?target=salesman_plans', 100),
(1012, '1102', 'lbl_commissions', 'index.php?target=salesman_commissions', 200),
(1015, '1104', 'lbl_salesman_accounts', 'index.php?target=salesman_report', 500),
(1016, '1105', 'lbl_payment_upload', 'index.php?target=payment_upload', 600),
(1017, '1106', 'lbl_banners', 'index.php?target=salesman_banners', 700),
(1018, '1107', 'lbl_multi_tier_affiliates', 'index.php?target=salesman_level_commissions', 800),
(1019, '1108', 'lbl_affiliate_statistics', 'index.php?target=banner_info', 900),
(1020, '1109', 'lbl_advertising_campaigns', 'index.php?target=salesman_adv_campaigns', 1000),
(1022, '1111', 'lbl_discounts', 'index.php?target=discounts', 3000),
(1023, '1112', 'lbl_targets_premiums', 'index.php?target=targets', 4000),
(1024, '1200', 'lbl_categories', 'index.php?target=categories', 100),
(1025, '1201', 'lbl_manufacturers', 'index.php?target=manufacturers', 170),
(1026, '', 'lbl_wish_lists', 'index.php?target=wishlists', 180),
(1060, '0801', 'lbl_users_C', 'index.php?target=user_C', 10),
(1029, '1202', 'lbl_warranties', 'index.php?target=warranties', 180),
(2287, '2501', 'lbl_install_addon', 'index.php?target=addons_manager', 40),
(1037, '', 'lbl_edit_ratings', 'index.php?target=ratings_edit', 0),
(1125, '2508', 'lbl_memberships', 'index.php?target=memberships', 10),
(1040, '', 'lbl_gift_certificates', 'index.php?target=giftcerts', 0),
(1041, '', 'lbl_subscriptions_info', 'index.php?target=subscriptions', 0),
(1140, '2501', 'lbl_addons', 'index.php?target=configuration&mode=addons', 20),
(1043, '2505', 'lbl_taxing_zones', 'index.php?target=zones', 0),
(1082, '2504', 'lbl_shipping', 'index.php?target=shipping', 20),
(2289, '1300', 'lbl_import_xcart', 'index.php?target=import&mode=xcart', 0),
(1047, '', 'lbl_rma_statuses', 'index.php?target=rma_statuses', 0),
(1049, '20', 'lbl_docs_info_I', 'index.php?target=docs_I', 10),
(1061, '0801', 'lbl_user_modify_C', 'index.php?target=user_C&mode=modify&user={$_GET[''user'']}', 20),
(1054, '', 'lbl_titles', 'index.php?target=titles', 50),
(1055, '', 'lbl_survey_surveys', 'index.php?target=surveys', 60),
(1058, '2400', 'lbl_modify_company', '', 20),
(1062, '__0801', 'lbl_user_delete_C', '', 15),
(1063, '2502', 'lbl_users_C', 'index.php?target=user_profiles', 10),
(1067, '2601', 'lbl_customer_side_news', 'index.php?target=news_c', 40),
(1066, '2600', 'lbl_news_management', 'index.php?target=news', 10),
(1068, '2600', 'lbl_modify_news', '', 20),
(1069, '2601', 'lbl_modify_customer_side_news', '', 50),
(1070, '__2600', 'lbl_create_news', 'index.php?target=news&list_id=', 30),
(1071, '2601', 'lbl_create_customer_side_news', 'index.php?target=news_c&list_id=', 60),
(1072, '', 'lbl_create_news', '2', 0),
(1073, '__18', 'lbl_doc_create_O', 'index.php?target=docs_O&action=add', 30),
(1074, '18', 'lbl_doc_info_O', '', 20),
(1075, '120300', 'lbl_products', 'index.php?target=products', 200),
(1076, '120300', 'lbl_product_modify', 'index.php?target=products&mode=details&product_id={$product_id}', 300),
(1077, '1100', 'lbl_products', 'index.php?target=products', 10),
(1078, '1100', 'lbl_product_modify', '', 20),
(1081, '20', 'lbl_doc_info_I', '', 20),
(1083, '2504', 'lbl_carriers', 'index.php?target=shipping_carriers', 10),
(1084, '2504', 'lbl_cod_types', 'index.php?target=cod_types', 50),
(1093, '1200', 'lbl_modify_category', '', 150),
(1097, '2904', 'lbl_edit_language', '', 20),
(1095, '1200', 'lbl_delete_category', '', 110),
(1096, '1200', 'lbl_add_category', '', 160),
(1098, '2904', 'lbl_languages', 'index.php?target=languages', 10),
(1101, '', 'lbl_payment_settings', '', 0),
(1107, '__120300', 'lbl_add_product', 'index.php?target=products&mode=add', 310),
(1108, '2507', 'lbl_countries', 'index.php?target=countries', 10),
(1109, '2507', 'lbl_cities', '', 20),
(1110, '2507', 'lbl_states', '', 30),
(1204, '2507', 'lbl_regions', '', 15),
(1111, '2507', 'lbl_county', '', 40),
(1113, '06', 'lbl_user_modify_G', 'index.php?target=user_G&mode=modify&user={$_GET[''user'']}', 20),
(1117, '__02', 'lbl_user_create_A', 'index.php?target=user_A&mode=add', 40),
(1118, '02', 'lbl_user_modify_A', '', 30),
(1119, '02', 'lbl_user_delete_A', '', 20),
(1120, '02', 'lbl_users_A', 'index.php?target=user_A', 10),
(1121, '__03', 'lbl_user_create_P', 'index.php?target=user_P&mode=add', 40),
(1122, '03', 'lbl_user_modify_P', '', 30),
(1123, '03', 'lbl_user_delete_P', '', 20),
(1124, '03', 'lbl_users_P', 'index.php?target=user_P', 10),
(1127, '18', 'lbl_docs_info_O', 'index.php?target=docs_O', 0),
(1128, '1205', 'lbl_special_sections', 'index.php?target=special_sections', 350),
(1139, '2505', 'lbl_taxes', 'index.php?target=taxes', 0),
(1141, '2501', 'lbl_addon_settings', '', 30),
(1142, '', 'lbl_configuration', '', 0),
(1143, '2500', 'lbl_configuration', 'index.php?target=settings', 0),
(1144, '2800', 'lbl_seo_settings', 'index.php?target=meta_tags', 0),
(1146, '1200', 'lbl_order_info', '', 20),
(1147, '1200', 'lbl_orders', 'index.php?target=docs_O', 10),
(1148, '1101', 'lbl_ean_serials', 'index.php?target=ean_serials', 30),
(1149, '1300', 'lbl_invoice', '', 20),
(1150, '1400', 'lbl_ship_doc_info', '', 20),
(1151, '1300', 'lbl_invoices', 'index.php?target=docs_I', 10),
(1152, '1400', 'lbl_ship_docs', 'index.php?target=docs_S', 10),
(1153, '1500', 'lbl_shipping_rates', 'index.php?target=shipping', 0),
(1154, '1501', 'lbl_shipping_zones', 'index.php?target=zones', 0),
(1155, '1100', 'lbl_user_create_B', 'index.php?target=user_B&mode=add', 40),
(1156, '1100', 'lbl_user_modify_B', '', 30),
(1157, '__06', 'lbl_user_add_G', 'index.php?target=user_G&mode=add', 100),
(1158, '1100', 'lbl_user_delete_B', '', 20),
(1159, '1100', 'lbl_users_B', 'index.php?target=user_B', 10),
(1221, '2905', 'lbl_css_styles', 'index.php?target=custom_css', 10),
(1160, '', 'lbl_user_modify_C', '', 20),
(1161, '', 'lbl_user_C', 'index.php?target=user_C', 10),
(1164, '', 'lbl_docs_info_O', 'index.php?target=docs_O', 10),
(1166, '', 'lbl_docs_info_S', 'index.php?target=docs_S', 10),
(1165, '', 'lbl_docs_info_I', 'index.php?target=docs_I', 10),
(1169, '', 'lbl_doc_info_S', '', 20),
(1167, '', 'lbl_doc_info_O', '', 20),
(1168, '', 'lbl_doc_info_I', '', 20),
(1177, '', 'lbl_affiliates_tree', 'index.php?target=affiliates', 0),
(1170, '', 'lbl_product_html_code', 'index.php?target=products', 10),
(1171, '', 'lbl_banner_html_code', 'index.php?target=banners', 30),
(1172, '', 'lbl_banners_statistics', 'index.php?target=banner_info', 40),
(1174, '', 'lbl_referred_sales', 'index.php?target=referred_sales', 0),
(1175, '', 'lbl_summary_statistics', 'index.php?target=stats', 0),
(1176, '', 'lbl_payment_history', 'index.php?target=payment_history', 0),
(1178, '', 'lbl_product_info', '', 20),
(1181, '', 'lbl_docs_info_B', 'index.php?target=docs_B', 10),
(1182, '', 'lbl_doc_info_B', '', 20),
(1184, '', 'lbl_doc_create_B', 'index.php?target=docs_B&action=add', 30),
(1275, '2504', 'lbl_shipping_rates', 'index.php?target=shipping_rates', 40),
(1268, '', 'lbl_gift_certificate', 'index.php?target=gifts&mode=giftcert', 40),
(1269, '1209', 'lbl_discount_coupons', 'index.php?target=coupons', 700),
(1200, '1200', 'lbl_category_products', '', 120),
(1234, '18', 'lbl_shipping_label_generator', '', 40),
(2302, '2801', 'lbl_clean_urls_list', 'index.php?target=clean_urls_list', 10),
(1215, '1000', 'lbl_warehouse_movements', 'index.php?target=movements', 10),
(1206, '__20', 'lbl_doc_create_I', 'index.php?target=docs_I&action=add', 30),
(1208, '06', 'lbl_user_delete_G', '', 30),
(1209, '06', 'lbl_users_G', 'index.php?target=user_G', 10),
(1210, '0700', 'lbl_doc_info_G', '', 30),
(1211, '', '11', '', 0),
(1212, '', '11', '', 0),
(1213, '__0700', 'lbl_doc_create_G', 'index.php?target=docs_G&action=add', 40),
(1214, '0700', 'lbl_docs_info_G', 'index.php?target=docs_G', 10),
(1217, '1001', 'lbl_docs_search_D', 'index.php?target=docs_D', 20),
(1218, '1001', 'lbl_doc_info_D', '', 30),
(1219, '0701', 'lbl_pos_orders_report', 'index.php?target=docs_cash_report', 50),
(1220, '2508', 'lbl_access_level', '', 15),
(1223, '2905', 'lbl_special_images', 'index.php?target=special_images', 20),
(1225, '1208', 'lbl_offers', 'index.php?target=offers', 600),
(1226, '2905', 'lbl_speed_bar', 'index.php?target=speed_bar', 30),
(1235, '31', 'lbl_surveys', 'index.php?target=surveys', 10),
(1236, '__31', 'lbl_create_survey', 'index.php?target=surveys&action=create', 30),
(1237, '31', 'lbl_modify_survey', '', 20),
(1238, '0802', 'lbl_giftcerts', 'index.php?target=giftcerts', 100),
(1239, '', 'lbl_wish_list', 'index.php?target=gifts', 10),
(1240, '', 'lbl_gift_registry', 'index.php?target=gifts&mode=events', 30),
(1241, '', 'lbl_friends_wish_list', 'index.php?target=gifts&mode=friends', 20),
(1256, '', 'lbl_additional_sections', 'index.php?target=user_profiles&mode=fields', 100),
(1247, '', 'lbl_divisions', 'index.php?target=divisions', 0),
(1250, '2502', 'lbl_users_A', 'index.php?target=user_profiles&user_type=A', 40),
(2291, '1300', 'lbl_import_data', 'index.php?target=import&mode=impdata', 0),
(2298, '1300', 'lbl_export_data', 'index.php?target=import&mode=expdata', 0),
(1260, '', 'lbl_modify_payment_method', '', 60),
(2282, '', 'lbl_ab_ad_banners', 'index.php?target=ad_banners&mode=list', 100),
(1259, '2503', 'lbl_payment_methods', 'index.php?target=payments&mode=methods', 30),
(1261, '', 'lbl_payment_method_add', 'index.php?target=payments&mode=methods&payment_id=', 70),
(1267, '0701', 'bl_pos_docs_report_pdf', 'index.php?target=docs_cash_report&mode=pdf', 60),
(1276, '2504', 'lbl_shipping_zones', 'index.php?target=shipping_zones', 30),
(1277, '2906', 'lbl_static_pages', 'index.php?target=pages', 10),
(1278, '__2906', 'lbl_add_static_page', 'index.php?target=pages&page_id=', 30),
(1279, '2906', 'lbl_modify_static_page', '', 20),
(1282, '32', 'lbl_domains', 'index.php?target=domains', 0),
(1283, '31', 'lbl_feature_classes', 'index.php?target=attributes', 800),
(1284, '31', 'lbl_features', 'index.php?target=attributes&mode=att', 900),
(1285, '2504', 'lbl_shipping_add', 'index.php?target=shipping&shipping_id=', 22),
(1286, '2504', 'lbl_shipping_modify', 'index.php?target=shipping&shipping_id={$shipping_id}', 21),
(2283, '', 'lbl_ab_banners_search', 'index.php?target=ad_banners&mode=search', 200),
(2284, '', 'lbl_ab_add_new_banner', 'index.php?target=ad_banner&mode=add', 300),
(2285, '', 'lbl_ab_update_banner', 'index.php?target=ad_banner&mode=update&banner_id={$banner_id}', 400),
(2292, '', 'lbl_sitemap_xml', 'index.php?target=sitemap_xml', 100),
(2288, '', 'lbl_filetypes', 'index.php?target=filetypes', 1000),
(2294, '2501', 'lbl_license', 'index.php?target=license', 50),
(2295, '33', 'lbl_ps_offers', 'index.php?target=promosuite', 0),
(2296, '__33', 'lbl_ps_new_offer', 'index.php?target=promosuite&action=form&offer_id=', 20),
(2297, '33', 'lbl_ps_modify_offer', '', 10),
(2299, '1300', 'lbl_google_export', 'index.php?target=google_base', 0),
(2300, '1300', 'lbl_ebay_export', 'index.php?target=ebay_export', 0),
(2301, '1300', 'lbl_amazon_export', 'index.php?target=amazon_export', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `cw_navigation_targets`
--

DROP TABLE IF EXISTS `cw_navigation_targets`;
CREATE TABLE IF NOT EXISTS `cw_navigation_targets` (
  `target_id` int(11) NOT NULL AUTO_INCREMENT,
  `target` varchar(64) NOT NULL DEFAULT '',
  `params` text NOT NULL,
  `visible` text NOT NULL,
  `section_id` int(11) NOT NULL DEFAULT '0',
  `tab_id` int(11) NOT NULL DEFAULT '0',
  `orderby` int(11) NOT NULL DEFAULT '0',
  `addon` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`target_id`),
  KEY `target` (`target`),
  KEY `tab_id` (`tab_id`),
  KEY `so` (`section_id`,`orderby`),
  KEY `tot` (`target_id`,`orderby`,`target`),
  KEY `taro` (`target`,`orderby`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2366 ;

--
-- Дамп данных таблицы `cw_navigation_targets`
--

INSERT INTO `cw_navigation_targets` (`target_id`, `target`, `params`, `visible`, `section_id`, `tab_id`, `orderby`, `addon`) VALUES
(66, 'user_C', '', '', 62, 1060, 40, ''),
(69, 'user_C', '$_GET[''mode'']==''add''', '', 62, 1006, 10, ''),
(192, 'special_sections', '', '', 66, 1128, 500, ''),
(75, 'salesman_plans', '', '', 65, 1011, 100, ''),
(76, 'salesman_commissions', '', '', 65, 1012, 200, ''),
(79, 'salesman_report', '', '', 65, 1015, 500, ''),
(80, 'payment_upload', '', '', 65, 1016, 600, ''),
(81, 'salesman_banners', '', '', 65, 1017, 700, ''),
(82, 'salesman_level_commissions', '', '', 65, 1018, 800, ''),
(83, 'banner_info', '', '', 65, 1019, 900, ''),
(84, 'salesman_adv_campaigns', '', '', 65, 1020, 1000, ''),
(86, 'discounts', '', '', 65, 1022, 3000, ''),
(87, 'targets', '', '', 65, 1023, 4000, ''),
(88, 'categories', '', '', 66, 1024, 130, ''),
(89, 'manufacturers', '', '', 66, 1025, 170, 'manufacturers'),
(91, 'products', '', '', 66, 1075, 250, ''),
(93, 'warranties', '', '', 66, 1029, 180, ''),
(2350, 'addons_manager', '', '', 95, 2287, 40, ''),
(103, 'docs_O', '', '', 71, 1127, 40, ''),
(106, 'zones', '', '', 94, 1043, 0, ''),
(149, 'shipping', '', '', 83, 1082, 10, 'shipping_system'),
(2352, 'import', '$_GET[''mode'']==''xcart''', '', 93, 2289, 5, ''),
(113, 'docs_I', '', '', 73, 1049, 30, ''),
(137, 'user_profiles', '', '', 79, 1063, 100, ''),
(117, 'news', '', '', 80, 1066, 10, ''),
(135, 'user_C', '$_GET[''mode'']==''modify'' && !empty($_GET[''user''])', '!empty($_GET[''user''])', 62, 1061, 20, ''),
(119, 'news', '!empty($list_id)', '!empty($list_id)', 80, 1068, 5, ''),
(120, 'news_c', '', '', 81, 1067, 130, ''),
(121, 'news_c', '!empty($list_id)', '!empty($list_id)', 81, 1069, 110, ''),
(122, 'news_c', 'isset($list_id)', '', 81, 1071, 120, ''),
(132, 'companies', '', '', 78, 1057, 30, ''),
(133, 'companies', '$_GET[''mode'']==''edit'' && $company_id', '$_GET[''mode'']==''edit'' && $company_id', 78, 1058, 10, ''),
(134, 'companies', '$_GET[''mode'']==''add''', '', 78, 1059, 20, ''),
(136, 'user_C', '$_GET[''mode''] == ''delete''', '$_GET[''mode''] == ''delete''', 62, 1062, 30, ''),
(140, 'news', 'isset($list_id)', '', 80, 1070, 6, ''),
(141, 'docs_O', '$_GET[''mode'']==''edit''', '', 71, 1073, 30, ''),
(142, 'docs_O', '!empty($_GET[''doc_id''])', '!empty($_GET[''doc_id''])', 71, 1074, 20, ''),
(143, 'products', '!empty($_GET[''product_id'']) && $_GET[''mode''] == ''details''', '!empty($_GET[''product_id'']) && $_GET[''mode''] == ''details''', 66, 1076, 220, ''),
(144, 'products', '', '', 82, 1077, 20, ''),
(145, 'products', '!empty($_GET[''product_id'']) && $_GET[''mode''] == ''details''', '!empty($_GET[''product_id'']) && $_GET[''mode''] == ''details''', 82, 1078, 10, ''),
(148, 'docs_I', '!empty($_GET[''doc_id''])', '!empty($_GET[''doc_id'']) && $_GET[''new'']!=''Y''', 73, 1081, 20, ''),
(150, 'shipping_carriers', '', '', 83, 1083, 20, 'shipping_system'),
(151, 'cod_types', '', '', 83, 1084, 30, 'shipping_system'),
(160, 'categories', '$_GET[''mode'']==''edit'' && !empty($cat)', '$_GET[''mode'']==''edit'' && !empty($cat)', 66, 1093, 110, ''),
(164, 'languages', '!empty($language)', '!empty($language)', 84, 1097, 0, ''),
(162, 'categories', '$_GET[''mode''] == ''delete''', '$_GET[''mode''] == ''delete''', 66, 1095, 90, ''),
(163, 'categories', '$_GET[''mode''] == ''add''', '$_GET[''mode''] == ''add''', 66, 1096, 100, ''),
(165, 'languages', '', '', 84, 1098, 0, ''),
(173, 'products', '$_GET[''mode''] == ''add''', '', 66, 1107, 230, ''),
(174, 'countries', '', '', 87, 1108, 40, ''),
(175, 'countries', '$_GET[''mode'']==''cities'' && !empty($country)', '$_GET[''mode'']==''cities'' && !empty($country)', 87, 1109, 20, ''),
(176, 'countries', '$_GET[''mode'']==''states'' && !empty($country)', '$_GET[''mode'']==''states'' && !empty($country)', 87, 1110, 30, ''),
(177, 'countries', '$_GET[''mode'']==''counties'' && !empty($country) && !empty($country)', '$_GET[''mode'']==''counties'' && !empty($country) && !empty($country)', 87, 1111, 10, ''),
(182, 'user_A', '$_GET[''mode'']==''add''', '', 89, 1117, 10, ''),
(183, 'user_A', '$_GET[''mode'']==''modify'' && !empty($_GET[''user''])', '$_GET[''mode'']==''modify'' && !empty($_GET[''user''])', 89, 1118, 20, ''),
(184, 'user_A', '$_GET[''mode''] == ''delete''', '$_GET[''mode''] == ''delete''', 89, 1119, 30, ''),
(185, 'user_A', '', '', 89, 1120, 40, ''),
(186, 'user_P', '$_GET[''mode'']==''add''', '', 90, 1121, 10, ''),
(187, 'user_P', '$_GET[''mode'']==''modify'' && !empty($_GET[''user''])', '$_GET[''mode'']==''modify'' && !empty($_GET[''user''])', 90, 1122, 20, ''),
(188, 'user_P', '$_GET[''mode''] == ''delete''', '$_GET[''mode''] == ''delete''', 90, 1123, 30, ''),
(189, 'user_P', '', '', 90, 1124, 40, ''),
(190, 'memberships', '', '', 91, 1125, 0, ''),
(203, 'taxes', '', '', 94, 1139, 0, ''),
(204, 'configuration', '', '', 95, 1140, 20, ''),
(205, 'configuration', '!empty($_GET[''module''])', '!empty($_GET[''module''])', 95, 1141, 10, ''),
(206, 'settings', '', '', 96, 1143, 0, ''),
(207, 'meta_tags', '', '', 97, 1144, 0, ''),
(209, 'docs_O', '!empty($_GET[''doc_id''])', '!empty($_GET[''doc_id''])', 98, 1146, 10, ''),
(210, 'docs_O', '', '', 98, 1147, 20, ''),
(211, 'ean_serials', '', '', 82, 1148, 30, ''),
(212, 'docs_I', '!empty($_GET[''doc_id''])', '!empty($_GET[''doc_id''])', 99, 1149, 10, ''),
(213, 'docs_S', '!empty($_GET[''doc_id''])', '!empty($_GET[''doc_id''])', 100, 1150, 10, ''),
(214, 'docs_I', '', '', 99, 1151, 20, ''),
(215, 'docs_S', '', '', 100, 1152, 20, ''),
(216, 'shipping', '', '', 101, 1153, 20, ''),
(217, 'zones', '', '', 101, 1154, 10, ''),
(218, 'user_B', '$_GET[''mode'']==''add''', '', 65, 1155, 10, ''),
(219, 'user_B', '$_GET[''mode'']==''modify'' && !empty($_GET[''user''])', '$_GET[''mode'']==''modify'' && !empty($_GET[''user''])', 65, 1156, 20, ''),
(220, 'user_B', '$_GET[''mode''] == ''delete''', '$_GET[''mode''] == ''delete''', 65, 1158, 30, ''),
(221, 'user_B', '', '', 65, 1159, 40, ''),
(222, 'user_C', '$_GET[''mode'']==''modify'' && !empty($_GET[''user''])', '$_GET[''mode'']==''modify'' && !empty($_GET[''user''])', 102, 1160, 10, ''),
(223, 'user_C', '', '', 102, 1161, 20, ''),
(226, 'docs_O', '', '', 103, 1164, 20, ''),
(228, 'docs_S', '', '', 105, 1166, 20, ''),
(227, 'docs_I', '', '', 104, 1165, 20, ''),
(231, 'docs_S', '!empty($_GET[''doc_id''])', '!empty($_GET[''doc_id''])', 105, 1169, 10, ''),
(229, 'docs_O', '!empty($_GET[''doc_id''])', '!empty($_GET[''doc_id''])', 103, 1167, 10, ''),
(230, 'docs_I', '!empty($_GET[''doc_id''])', '!empty($_GET[''doc_id''])', 104, 1168, 10, ''),
(239, 'affiliates', '', '', 107, 1177, 0, ''),
(232, 'products', '', '', 106, 1170, 20, ''),
(233, 'banners', '', '', 106, 1171, 30, ''),
(234, 'banner_info', '', '', 106, 1172, 40, ''),
(236, 'referred_sales', '', '', 107, 1174, 0, ''),
(237, 'stats', '', '', 107, 1175, 0, ''),
(238, 'payment_history', '', '', 107, 1176, 0, ''),
(240, 'products', '!empty($_GET[''product_id'']) && $_GET[''mode''] == ''details''', '!empty($_GET[''product_id'']) && $_GET[''mode''] == ''details''', 106, 1178, 10, ''),
(243, 'docs_B', '', '', 109, 1181, 30, ''),
(244, 'docs_B', '!empty($_GET[''doc_id''])', '!empty($_GET[''doc_id''])', 109, 1182, 10, ''),
(246, 'docs_B', '$_GET[''mode''] == ''add''', '', 109, 1184, 20, ''),
(331, 'gifts', '$_GET[''mode'']==''giftcert''', '', 116, 1268, 30, ''),
(332, 'coupons', '', '', 66, 1269, 700, 'discount_coupons'),
(262, 'categories', '$_GET[''mode'']==''products''', '$_GET[''mode'']==''products''', 66, 1200, 120, ''),
(278, 'movements', '', '', 113, 1215, 40, ''),
(266, 'countries', '$_GET[''mode'']==''regions'' && !empty($country)', '$_GET[''mode'']==''regions'' && !empty($country)', 87, 1204, 0, ''),
(268, 'docs_I', '!empty($_GET[''new''])', '', 73, 1206, 10, ''),
(270, 'user_G', '$_GET[''mode'']==''add''', '', 111, 1157, 10, ''),
(271, 'user_G', '$_GET[''mode'']==''modify'' && !empty($_GET[''user''])', '!empty($_GET[''user''])', 111, 1113, 20, ''),
(272, 'user_G', '$_GET[''mode''] == ''delete''', '$_GET[''mode''] == ''delete''', 111, 1208, 30, ''),
(273, 'user_G', '', '', 111, 1209, 40, ''),
(274, 'docs_G', '!empty($_GET[''doc_id''])', '!empty($_GET[''doc_id''])', 112, 1210, 10, ''),
(275, 'docs_cach_sellings', '', '', 68, 1212, 0, ''),
(276, 'docs_G', '$_GET[''mode'']==''add''', '', 112, 1213, 20, ''),
(277, 'docs_G', '', '', 112, 1214, 30, ''),
(280, 'docs_D', '', '', 113, 1217, 30, ''),
(281, 'docs_D', '!empty($_GET[''doc_id''])', '!empty($_GET[''doc_id''])', 113, 1218, 20, ''),
(282, 'docs_cash_report', '', '', 112, 1219, 40, ''),
(283, 'access_level', '', '$_GET[''target'']==''access_level''', 91, 1220, 0, ''),
(284, 'custom_css', '', '', 114, 1221, 10, ''),
(286, 'special_images', '', '', 114, 1223, 20, ''),
(288, 'offers', '', '', 66, 1225, 600, 'special_offers'),
(289, 'speed_bar', '', '', 114, 1226, 30, 'EStore'),
(297, 'generator', '', '$_GET[''target'']==''generator''', 71, 1234, 0, 'shipping_label_generator'),
(298, 'surveys', '', '', 115, 1235, 30, ''),
(299, 'surveys', '$_GET[''action''] == ''create''', '', 115, 1236, 10, ''),
(300, 'surveys', '!empty($_GET[''survey_id''])', '!empty($_GET[''survey_id''])', 115, 1237, 20, ''),
(301, 'giftcerts', '', '', 62, 1238, 0, 'estore_gift'),
(302, 'gifts', '', '', 116, 1239, 110, ''),
(303, 'gifts', '$_GET[''mode'']==''events''', '', 116, 1240, 10, ''),
(304, 'gifts', '$_GET[''mode'']==''friends''', '', 116, 1241, 20, ''),
(306, 'ws', '', '', 117, 1243, 20, 'WarehouseSharing'),
(307, 'ws', '$_GET[''mode''] == ''info''', '', 117, 1244, 10, 'WarehouseSharing'),
(310, 'divisions', '', '', 90, 1247, 0, ''),
(313, 'user_profiles', '$_GET[''user_type'']==''A''', '', 79, 1250, 30, ''),
(2354, 'import', '$_GET[''mode'']==''impdata''', '', 93, 2291, 5, ''),
(319, 'user_profiles', '$_GET[''mode'']==''fields''', '', 79, 1256, 90, ''),
(322, 'payments', '$mode==''methods''', '', 85, 1259, 70, 'payment_system'),
(323, 'payments', '$mode==''methods'' && !empty($payment_id)', '$mode==''methods'' && !empty($payment_id)', 85, 1260, 60, 'payment_system'),
(324, 'payments', '$mode==''methods'' && isset($payment_id) && empty($payment_id)', '', 85, 1261, 50, 'payment_system'),
(330, 'docs_cash_report', '$_GET[''mode''] == ''pdf''', '', 112, 1267, 39, ''),
(338, 'shipping_rates', '', '', 83, 1275, 50, 'shipping_system'),
(339, 'shipping_zones', '', '', 83, 1276, 60, 'shipping_system'),
(340, 'pages', '!isset($_GET[''page_id''])', '', 121, 1277, 0, ''),
(341, 'pages', 'isset($_GET[''page_id'']) && empty($_GET[''page_id''])', '', 121, 1278, 0, ''),
(342, 'pages', 'isset($_GET[''page_id'']) && !empty($_GET[''page_id''])', '!empty($_GET[''page_id''])', 121, 1279, 0, ''),
(345, 'domains', '', '', 122, 1282, 0, ''),
(346, 'attributes', '', '', 66, 1283, 900, ''),
(347, 'attributes', 'in_array($_GET[''mode''], array(''att'', ''add_att''))', '', 66, 1284, 800, ''),
(348, 'shipping', 'isset($_GET[''shipping_id'']) && empty($_GET[''shipping_id''])', '', 83, 1285, 9, 'shipping_system'),
(349, 'shipping', 'isset($_GET[''shipping_id'']) && !empty($_GET[''shipping_id''])', '!empty($shipping_id)', 83, 1286, 9, 'shipping_system'),
(2345, 'ad_banners', '$mode==''list''', '', 2122, 2282, 100, 'ad_banners'),
(2346, 'ad_banners', '$mode==''search''', '', 2122, 2283, 200, 'ad_banners'),
(2347, 'ad_banner', '$mode==''add''', '', 2122, 2284, 300, 'ad_banners'),
(2348, 'ad_banner', '$mode==''update'' && !empty($banner_id)', '$mode==''update'' && !empty($banner_id)', 2122, 2285, 400, 'ad_banners'),
(2355, 'sitemap_xml', '', '', 2124, 2292, 100, 'sitemap_xml'),
(2351, 'filetypes', '', '', 66, 2288, 100, 'ppd'),
(2357, 'license', '', '', 95, 2294, 0, ''),
(2358, 'promosuite', '', '', 2125, 2295, 20, 'promotion_suite'),
(2359, 'promosuite', 'isset($_GET[''offer_id'']) && empty($_GET[''offer_id''])', '', 2125, 2296, 0, 'promotion_suite'),
(2360, 'promosuite', 'isset($_GET[''offer_id'']) && !empty($_GET[''offer_id''])', 'isset($_GET[''offer_id'']) && !empty($_GET[''offer_id''])', 2125, 2297, 10, 'promotion_suite'),
(2361, 'import', '$_GET[''mode'']==''expdata''', '', 2127, 2298, 0, ''),
(2365, 'clean_urls_list', '', '', 97, 2302, 10, 'clean_urls'),
(2362, 'google_base', '', '', 2127, 2299, 0, 'google_base'),
(2363, 'ebay_export', '', '', 2127, 2300, 0, 'ebay'),
(2364, 'amazon_export', '', '', 2127, 2301, 0, 'amazon');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;