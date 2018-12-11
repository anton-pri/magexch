{if !$included}
<form action="index.php?target={$current_target}&user={$user}&address_id={$address_id}" method="post" name="address_form">
<input type="hidden" name="action" value="" />
<input type="hidden" name="mode" value="{$mode}" />
{/if}
{include file='admin/users/sections/address.tpl'}
{if !$included}
</form>

{if $address_id}
{include file='buttons/button.tpl' button_title=$lng.lbl_update href="javascript:cw_submit_form('address_form', 'update_address');" acl=$page_acl}
{else}
{include file='buttons/button.tpl' button_title=$lng.lbl_add_new href="javascript:cw_submit_form('address_form', 'update_address');" acl=$page_acl}
{/if}
{include file='buttons/button.tpl' button_title=$lng.lbl_addresses_list href="index.php?target=`$current_target`&mode=`$mode`&user=`$user`"}
{/if}
