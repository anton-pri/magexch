UPDATE cw_dashboard SET active=0 WHERE name='search';
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_doc_details', 'Payment information', 'Labels'); 
REPLACE INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_doc_notes', 'Order notes', 'Labels');
