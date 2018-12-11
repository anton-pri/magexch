<?php
// CartWorks.com - Promotion Suite 
if (!defined('XCART_START')) { header("Location: ../../"); die("Access denied"); }
if (empty($addons['Promotion_Suite'])) return false;

include_once $xcart_dir.'/addons/Promotion_Suite/user_bonuses.php';

            foreach ($products as $k => $v) {
                $products[$k]['special_offer'] = (($_bid = cw_query_first_cell("SELECT bonusid FROM $tables[bonus_conditions] WHERE type='P' AND objid='$v[productid]' AND bonusid IN ('".join("','",array_keys($user_bonuses))."')"))>0?$user_bonuses[$_bid]:0);
            }
// CartWorks.com - Promotion Suite 
?>
