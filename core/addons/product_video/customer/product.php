<?php
namespace cw\product_video;

global $product_video;

$product_video = cw_call('cw\\'.addon_name.'\\get_product_video', array($product_id));

$smarty->assign_by_ref('product_video', $product_video);
