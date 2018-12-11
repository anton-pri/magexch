truncate table cw_clean_urls_history;
ALTER TABLE cw_clean_urls_history DROP INDEX id;
ALTER TABLE `cw_clean_urls_history` ADD INDEX ( `item_type` ) ;
ALTER TABLE `cw_clean_urls_history` ADD INDEX `url` ( `url` ( 8 ) );
