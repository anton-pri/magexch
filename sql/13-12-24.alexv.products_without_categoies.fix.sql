UPDATE cw_navigation_targets
SET visible = IF (visible = '', '$config[\'Appearance\'][\'categories_in_products\'] == \'1\'', CONCAT(visible, ' && $config[\'Appearance\'][\'categories_in_products\'] == \'1\''))
WHERE target = 'categories';