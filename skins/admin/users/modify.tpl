{include_once_src file='main/include_js.tpl' src='js/register.js'}
{capture name="customer_section"}
<script type="text/javascript">
  $(document).ready(cw_register_init);
  reg_usertype = '{$userinfo.usertype}';
</script>

<div class="block">
	<div class="block-content block-content-full">
		{include file='admin/users/service_data.tpl'}
	</div>
</div>

<form name="profile_form" action="index.php?target={$current_target}&user={$user}" method="post" id='profile_form' enctype="multipart/form-data">
<input type="hidden" name="js_tab" id="form_js_tab" value="">
<input type="hidden" name="mode" value="{$mode}">
<input type="hidden" name="action" value="update">

{foreach from=$profile_sections key=section_name item=section}
    {if $section.is_avail}
        {capture name='profile_section'}
            {if !$section.is_default}
                {include file="admin/users/sections/custom.tpl" included_tab=$section.id }
            {else}
                {include file="admin/users/sections/`$section_name`.tpl" included_tab=$section.id}
            {/if}
        {/capture}
        {include file='admin/wrappers/block.tpl' is_dialog=0 content=$smarty.capture.profile_section title=$section.section_title}
    {/if}
{/foreach}

{include file="admin/buttons/button.tpl" button_title=$lng.lbl_save href="javascript: cw_submit_form('profile_form');" style="btn-green"}

{if $user && $userinfo.usertype eq 'C' && $current_area eq 'A' && $userinfo.custom_fields_by_name.suspend_account eq 'Y'}
<br>
<br>
{*<input type="hidden" name="suspend_account_customer_id" value="{$customer_id}" />*}
{include file="admin/buttons/button.tpl" button_title=$lng.lbl_suspend_account href="javascript: if (confirm('Suspend this account and delete all personal data from the profile and related orders? Warning: this operation is not reversible!')) cw_submit_form('profile_form', 'suspend_account');" style="btn-danger"}
{/if}
</form>

<br />
<br />
{if $user && $userinfo.usertype eq 'C'}
    {capture name='profile_section'}
        {include file='admin/users/sections/purchased_products.tpl'}
    {/capture}
    {include file='admin/wrappers/block.tpl' is_dialog=0 content=$smarty.capture.profile_section title=$lng.lbl_products}
{/if}
{/capture}


{if $smarty.get.target eq "user_C"}
    {if $user}
        {*include file='common/page_title.tpl' title=$lng.lbl_modify_customer*}
        {include file='admin/wrappers/section.tpl' content=$smarty.capture.customer_section title=$lng.lbl_modify_customer}
    {else}
        {*include file='common/page_title.tpl' title=$lng.lbl_create_customer*}
        {include file='admin/wrappers/section.tpl' content=$smarty.capture.customer_section title=$lng.lbl_create_customer}

    {/if}
{else}
    {if $user}
        {if $current_user_type}
            {assign var=lbl_ident value="lbl_user_modify_`$current_user_type`"}
            {*include file='common/page_title.tpl' title=$lng.$lbl_ident*}
            {include file='admin/wrappers/section.tpl' content=$smarty.capture.customer_section title=$lng.$lbl_ident}
        {else}
            {*include file='common/page_title.tpl' title=$lng.lbl_modify_admin*}
            {include file='admin/wrappers/section.tpl' content=$smarty.capture.customer_section title=$lng.lbl_modify_admin}

        {/if}
    {else}
        {if $current_user_type}
            {assign var=lbl_ident value="lbl_user_create_`$current_user_type`"}
            {*include file='common/page_title.tpl' title=$lng.$lbl_ident*}
            {include file='admin/wrappers/section.tpl' content=$smarty.capture.customer_section title=$lng.$lbl_ident}
        {else}
            {*include file='common/page_title.tpl' title=$lng.lbl_create_admin*}
            {include file='admin/wrappers/section.tpl' content=$smarty.capture.customer_section title=$lng.lbl_create_admin}
        {/if}
    {/if}
{/if}
