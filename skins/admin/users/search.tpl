{capture name="section"}
{capture name="block"}

{* TODO: Implement as just tpl or even beter remove at all replacing by jQuery plugin *}
{*
{jstabs name='user_search'}
default_tab="{$js_tab|default:"basic_search"}"
default_template="main/users/search_form.tpl"

[submit]
title="{$lng.lbl_search}"
href="javascript: cw_submit_form('search_form');"
style="btn-green push-20 push-5-r"

[reset]
title="{$lng.lbl_reset}"
href="javascript: cw_submit_form(document.search_form, 'reset');"
style="btn-green push-20 push-5-r"

[basic_search]
{assign var='lbl' value="lbl_search_user_`$current_search_type`"}
title="{$lng.$lbl}"

[adv_search_address]
title={$lng.lbl_search_customer_by_address}

[adv_search_tax]
title={$lng.lbl_adv_customer_search_admin}

[adv_search_web]
title={$lng.lbl_adv_customer_search_web}

[adv_search_mailing]
title={$lng.lbl_adv_customer_search_mailing}

{/jstabs}
*}

{literal}
<script type='text/javascript'>
$(document).ready(function() {
    function toggle_active_section() {
        var id = $(this).attr('id')+'_section';
        console.log(id);
        if ($(this).attr('checked')) {
            $('#'+id).show();
        } else {
            $('#'+id).hide();
        }
    };

    $('#active_sections').find('input').each(toggle_active_section);
	$('#active_sections').find('input').bind('click',function(){
      var id = $(this).attr('id')+'_section';
      $('#'+id).toggle();
    });



});
</script>
{/literal}
<script src="{$SkinDir}/js/navigation.js" type="text/javascript"></script>

<form name="search_form" action="index.php?target={$current_target}" method="post">
<input type="hidden" name="action" value="search" />
<input type="hidden" name="js_tab" id="form_js_tab" value="">



{include file='main/users/search_form.tpl' included_tab='basic_search'}
<div class='box form-horizontal' id='active_sections'>
<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_additional_criteria}:</label>
	<div class="col-xs-12">
		<div class="checkbox">
			<label><input type='checkbox' value='1' name='search_sections[adv_search_address]' id='adv_search_address' {if $search_prefilled.search_sections.adv_search_address} checked="checked"{/if} /> {$lng.lbl_search_customer_by_address}</label>
		</div>
		<div class="checkbox">
			<label><input type='checkbox' value='1' name='search_sections[adv_search_admin]' id='adv_search_admin' {if $search_prefilled.search_sections.adv_search_admin} checked="checked"{/if} /> {$lng.lbl_adv_customer_search_admin}</label>
		</div>
		<div class="checkbox">
			<label><input type='checkbox' value='1' name='search_sections[adv_search_orders]' id='adv_search_orders' {if $search_prefilled.search_sections.adv_search_orders} checked="checked"{/if} /> {$lng.lbl_orders}</label>
		</div>
	</div>
</div>
</div>
{include file='main/users/search_form.tpl' included_tab='adv_search_address'}
{include file='main/users/search_form.tpl' included_tab='adv_search_admin'}
{include file='main/users/search_form.tpl' included_tab='adv_search_orders'}

<div class="buttons">
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_search href="javascript: cw_submit_form('search_form');" style="btn-green push-20 push-5-r"}

{include file='admin/buttons/button.tpl' button_title=$lng.lbl_reset href="javascript: cw_submit_form('search_form', 'reset');" style="btn-danger push-20 push-5-r"}

{include file='admin/users/search_form_buttons.tpl'}

</div>

<div class="form-horizontal">
<div class="form-group">
	<div class="col-xs-6"><input type='text' class="form-control" name='save_search_name' id='save_search_name' value="{$current_loaded_search_name}" placeholder="{$lng.lbl_saved_search_name|default:'Saved search name'}" /></div>
	<div class="col-xs-6">{include file='admin/buttons/button.tpl' button_title=$lng.lbl_save_search|default:'Save search' href="javascript: cw_submit_form('search_form', 'save_search');" style="btn-green"}</div>
</div>
<div class="form-group">
	<div class="col-xs-6">
	  <select class="form-control" name="save_search_restore" title="{$lng.lbl_load_saved_search|default:"Load saved search"}">
		<option value="">empty</option>
		{foreach from=$saved_user_search item=ssi}
		<option value="{$ssi.ss_id}" {if $ssi.ss_id eq $current_loaded_search_id}selected="selected"{/if}>{$ssi.name|stripslashes}</option>
		{/foreach}
	  </select>
	</div>
	<div class="col-xs-6">
		{include file='admin/buttons/button.tpl' button_title=$lng.lbl_load|default:'Load' href="javascript: cw_submit_form('search_form', 'save_search_load');" style="btn-green"}
		{if $current_loaded_search_id gt 0}
		&nbsp;
		{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete href="javascript: cw_submit_form('search_form', 'delete_search_load');" style="btn-green"}
		&nbsp;
		{/if}
	</div>
</div>
</div>
{* TODO: Replace all cases to jq plugin and delete *}
{*include file='tabs/js_tabs.tpl' is_checkboxes=1 name="search_sections" value=$search_prefilled.search_sections*}
</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}


<a name='result'></a>
<div id='search_result' blockUI='search_result'>
{if $mode eq 'search'}
{include file='admin/users/search_results.tpl'}
{/if}
</div>
{/capture}
{if $smarty.get.target eq "user_C"}
    {include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_customers}

{else}
    {if $current_search_type}
        {assign var=lbl_ident value="lbl_users_`$current_search_type`"}
       {* <h1 class="title">{$lng.$lbl_ident}</h1>*}
    	{include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.$lbl_ident}

    {else}
       {* <h1 class="title">{$lng.lbl_admins}</h1>*}
        {include file="admin/wrappers/section.tpl" content=$smarty.capture.section title=$lng.lbl_admins}

    {/if}
{/if}
