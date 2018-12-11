<?php
// Time constants
define('CURRENT_TIME', time());
define('SECONDS_PER_HOUR',  3600);
define('SECONDS_PER_DAY',   86400);   // 60 * 60 * 24
define('SECONDS_PER_WEEK',  604800); // 60 * 60 * 24 * 7

// Product type
define('PRODUCT_TYPE_GENERAL',          1);
define('PRODUCT_TYPE_SERVICE_RESERVED', 2);
define('PRODUCT_TYPE_CONF_RESERVED',    3);
define('PRODUCT_TYPE_RMA',              10);

// Addons type (level)
define('ADDON_TYPE_CORE',       -1);
define('ADDON_TYPE_GENERAL',    0);
define('ADDON_TYPE_DEV',        1);
define('ADDON_TYPE_UNKNOWN',    2);

// Protection of attributes from admin interface (binary logic is applicable)
define('ATTR_PROTECTION_FIELD',     1); // Field name is pre-defined
define('ATTR_PROTECTION_TYPE',      2); // Attr type can't be changed
define('ATTR_PROTECTION_VALUES',    4); // Attr values are protected
define('ATTR_PROTECTION_DELETE',    8); // Attr can't be deleted
