<?php
global $product_id;

$product_info = cw_func_call(
					'cw_product_get', 
					array(
						'id' 			=> $product_id, 
						'user_account' 	=> $user_account, 
						'info_type' 	=> 65535
					)
				);

$smarty->assign('product', $product_info);
$smarty->assign('main', 'product_links');
?>
