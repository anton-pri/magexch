INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_ps_cond_weight', 'Shopping cart weight', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_ps_weight_interval', 'Weight products in the cart should be from', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_user_has_membership', 'The user has the membership', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_used_coupon', 'Used a coupon', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_user_used_specific_coupon', 'User has used a specific coupon', 'Labels');
INSERT INTO `cw_languages` (`code`, `name`, `value`, `topic`) VALUES ('EN', 'lbl_ps_disc_subtotal_interval', 'Discounted cart subtotal should be from', 'Labels');

ALTER TABLE `cw_ps_conditions` ADD `coupon` VARCHAR( 16 ) NOT NULL;