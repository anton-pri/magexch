UPDATE `cw_attributes` SET `is_required` = 0
WHERE (`field` = 'ebay_condition_id' OR `field` = 'ebay_category') AND `addon` = 'ebay';