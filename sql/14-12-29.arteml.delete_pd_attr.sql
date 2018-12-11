-- Skip this patch on printdrop.com

-- Delete printdrop.com attributes
DELIMITER ;
drop procedure if exists RemovePDattributes;

delimiter //
//
create procedure RemovePDattributes()
begin

    select @is_pd:=count(*) from cw_domains where skin like '%skins_printdro%';

    if !@is_pd then

select "It is not printdrop.com. Delete printdrop_com attributes" as msg;
delete from cw_attributes_values where attribute_id IN (select attribute_id from cw_attributes where addon='printdrop_com');
delete from cw_attributes_default where attribute_id IN (select attribute_id from cw_attributes where addon='printdrop_com');
delete from cw_attributes_images where id IN (select attribute_id from cw_attributes where addon='printdrop_com');
delete from  `cw_attributes_classes_assignement` where attribute_id IN (select attribute_id from cw_attributes where addon='printdrop_com');
delete from cw_attributes_lng where attribute_id IN (select attribute_id from cw_attributes where addon='printdrop_com');

delete from cw_attributes where addon='printdrop_com';

    else

select "It is printdrop.com. Skip patch..." as msg;

    end if;

end
//

delimiter ;
call RemovePDattributes();

drop procedure if exists RemovePDattributes;

update cw_config set variants="A:Company name | Product name\nD:Product name | Company name" where name='page_title_format';

