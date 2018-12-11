-- Mark all current single default values as default
update cw_attributes_default ad join cw_attributes a on a.attribute_id = ad.attribute_id and a.item_type = 'P' and a.type NOT IN ('multiple_selectbox','selectbox') SET ad.is_default='1';
update cw_languages set name='lbl_attributes_settings', value='Attributes settings' where name ='lbl_products_attributes_settings';

select @range_att_id:=attribute_id from cw_attributes where item_type='P' and field='predefined_ranges' and addon='core';
select @price_att_id:=attribute_id from cw_attributes where item_type='P' and field='price' and addon='core';

update cw_attributes_default set attribute_id=@price_att_id where attribute_id=@range_att_id;

delete from cw_attributes where attribute_id=@range_att_id;

update cw_attributes set protection=protection&~4 where field='price' and addon='core';
update cw_attributes set protection=protection&~4 where field='rating' and addon='estore_products_review';

