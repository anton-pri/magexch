{if !$customer_id}<a href="{$catalogs.customer}/index.php?target=help&section=login_customer" class='need_login'><i class="icon-lock"></i><span class="phone_hide">{$lng.lbl_login}</span></a>{else}<a href="{$catalogs.customer}/index.php?target=acc_manager&action=logout"><i class="icon-logout"></i><span class="phone_hide">{$lng.lbl_logoff}</span></a>{/if}