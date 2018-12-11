delete from cw_breadcrumbs where link='/index.php?target=promosuite&mode=details&action=details&offer_id=[[ANY]]';
select @bid:=breadcrumb_id from cw_breadcrumbs where link='/index.php?target=promosuite';
INSERT INTO `cw_breadcrumbs` ( `breadcrumb_id` , `link` , `title` , `parent_id` , `addon` , `uniting`) VALUES ( NULL , '/index.php?target=promosuite&mode=details&action=details&offer_id=[[ANY]]', 'lbl_ps_details', '1', 'promotion_suite', '0');
