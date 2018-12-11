-- Remove/Rename magazines addon
DELIMITER ;
drop procedure if exists RemoveMagazinesAddon;

delimiter //
//
create procedure RemoveMagazinesAddon()
begin

    select @is_saratoga:=count(*) from cw_domains where skin like '%skins_saratogawine%';

    if !@is_saratoga then
SELECT 'It is not Saratogawine. Start deletion of Magazines addon.';
DELETE FROM `cw_languages` WHERE name= 'lbl_magazines';
DELETE FROM `cw_languages` WHERE name= 'lbl_magazine_reviews';
DELETE FROM `cw_languages` WHERE name= 'addon_name_magazines';
DELETE FROM `cw_languages` WHERE name= 'addon_descr_magazines';
DELETE FROM `cw_languages` WHERE name= 'lbl_update_selected';
DELETE FROM `cw_languages` WHERE name= 'err_field_please_fill';
DELETE FROM `cw_languages` WHERE name= 'err_orders_field_number';
DELETE FROM `cw_languages` WHERE name= 'err_name_service_name_unique';

DELETE FROM `cw_attributes_values` WHERE attribute_id IN (SELECT attribute_id FROM cw_attributes WHERE addon='magazines');
DELETE FROM `cw_attributes_classes_assignement` WHERE attribute_id IN (SELECT attribute_id FROM cw_attributes WHERE addon='magazines');
DELETE FROM `cw_attributes_default` WHERE attribute_id IN (SELECT attribute_id FROM cw_attributes WHERE addon='magazines');
DELETE FROM `cw_attributes_lng` WHERE attribute_id IN (SELECT attribute_id FROM cw_attributes WHERE addon='magazines');
DELETE FROM `cw_attributes` WHERE addon='magazines';

DELETE FROM `cw_addons` WHERE addon='magazines';
DELETE FROM `cw_breadcrumbs` WHERE addon='magazines';
SELECT @tab_id:=tab_id FROM `cw_navigation_targets`  WHERE addon='magazines' AND target='magazines';
DELETE FROM `cw_navigation_tabs` WHERE tab_id = @tab_id;
DELETE FROM `cw_navigation_targets` WHERE addon = 'magazines';
DROP TABLE IF EXISTS `cw_magazines`;

    else

SELECT 'It is saratoga!!! Renaming Magazines addon into custom_saratogawine_magazines. Please update custom repo to get addon code and create necessary symlinks manually.';

UPDATE cw_addons SET addon='custom_saratogawine_magazines', parent='custom_saratogawine'  WHERE addon='magazines';
UPDATE cw_breadcrumbs SET addon='custom_saratogawine_magazines' WHERE addon='magazines';
UPDATE cw_navigation_targets SET addon='custom_saratogawine_magazines' WHERE addon='magazines';
UPDATE cw_attributes SET addon='custom_saratogawine_magazines' WHERE addon='magazines';
UPDATE cw_languages SET name='addon_name_custom_saratogawine_magazines' WHERE name= 'addon_name_magazines';
UPDATE cw_languages SET name='addon_descr_custom_saratogawine_magazines' WHERE name= 'addon_descr_magazines';


    end if;

end
//

delimiter ;
call RemoveMagazinesAddon();

drop procedure if exists RemoveMagazinesAddon;



-- Delete demo and printdrop addons
delete from cw_languages where name='addon_name_demo_module';
DELETE FROM `cw_addons` WHERE `cw_addons`.`addon` = 'demo_module';

select @pd:=addon FROM `cw_addons` WHERE `cw_addons`.`addon` = 'printdrop_com' and active=0;
DELETE FROM `cw_addons` WHERE `cw_addons`.`addon` = 'printdrop_com' and active=0;
delete from cw_languages where name='addon_name_printdrop_com' and @pd='printdrop_com';


-- Switch addons in dev -> ready
update cw_addons set status=0 where addon in (
'orders_extra_features',
'anti_scrapping_robot',
'now_online',
'catalog_product',
'product_video',
'promotion_suite',
'vertical_response',
'wordpress');

-- Switch addons in dev -> unknown
update cw_addons set status=2 where addon in (
'egoods',
'interneka',
'magnifier',
'seller');

