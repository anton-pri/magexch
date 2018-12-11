INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'msg_adm_products_reviews_del', 'Review for this product has been deleted', 'Text');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'msg_adm_products_reviews_upd', 'Review for this product has been updated', 'Text');

INSERT INTO `cw_attributes` (`attribute_id`, `name`, `type`, `field`, `is_required`, `active`, `orderby`, `module`, `item_type`, `is_sortable`, `is_comparable`, `is_show`, `pf_is_use`, `pf_orderby`, `pf_display_type`, `is_show_module`) 
VALUES (NULL, 'Product has reviews', 'checkbox', 'has_review', '0', '1', '110', 'EStoreProductsReview', 'P', '0', '0', '1', '1', '0', 'E', '0');

SET @attribute_id = LAST_INSERT_ID();

DELETE FROM cw_attributes_values WHERE attribute_id = @attribute_id;

INSERT INTO
    cw_attributes_values (`item_id`,`attribute_id`,`value`, `item_type`)
SELECT
    DISTINCT
    product_id,
    @attribute_id,
    'Yes',
    'P'
FROM
    cw_products_reviews;
