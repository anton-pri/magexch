<form action="index.php?target={$current_target}&mode={$mode}&user={$user}" method="post" name="addresses_form">
<input type="hidden" name="action" value="update_addresses" />
{include file='main/users/sections/addresses.tpl'}
</form>

{if $addresses}
{include file='buttons/button.tpl' button_title=$lng.lbl_update href="javascript:cw_submit_form('addresses_form');" acl=$page_acl}
{include file='buttons/button.tpl' button_title=$lng.lbl_delete href="javascript:cw_submit_form('addresses_form', 'delete');" acl=$page_acl}
{/if}
{include file='buttons/button.tpl' button_title=$lng.lbl_add_new href="index.php?target=`$current_target`&mode=`$mode`&user=`$user`&address_id=" acl=$page_acl}
