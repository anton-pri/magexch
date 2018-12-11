-- Disable speed bar without domain
select @aid:=attribute_id from cw_attributes where type='domain-selector' and item_type='B';
update cw_speed_bar sb set active='N' where sb.item_id NOT IN (select a.item_id from cw_attributes_values a where attribute_id=@aid);

-- Disable categories without domain
select @aid:=attribute_id from cw_attributes where type='domain-selector' and item_type='C';
update cw_categories set status='0' where category_id NOT IN (select a.item_id from cw_attributes_values a where attribute_id=@aid);

-- Disable manuf without domain
select @aid:=attribute_id from cw_attributes where type='domain-selector' and item_type='M';
update cw_manufacturers set avail='0' where manufacturer_id NOT IN (select a.item_id from cw_attributes_values a where attribute_id=@aid);

-- Disable cms without domain
select @aid:=attribute_id from cw_attributes where type='domain-selector' and item_type='AB';
update cw_cms set active='N' where contentsection_id NOT IN (select a.item_id from cw_attributes_values a where attribute_id=@aid);

-- Disable products without domain
select @aid:=attribute_id from cw_attributes where type='domain-selector' and item_type='P';
update cw_products set status=0 where product_id NOT IN (select a.item_id from cw_attributes_values a where attribute_id=@aid);

-- Disable offers without domain
select @aid:=attribute_id from cw_attributes where type='domain-selector' and item_type='PS';
update cw_ps_offers set active=0 where pid=0 and offer_id NOT IN (select a.item_id from cw_attributes_values a where attribute_id=@aid);

