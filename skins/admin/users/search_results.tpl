{if $navigation.total_items gt 0}
{capture name=section}
<div class="box">

{if $users}
<div class="col-xs-6">
	{include file='common/navigation_counter.tpl'}
</div>
<div class="col-xs-6">
    {include file='common/navigation.tpl'}
</div>

<form action="index.php?target={$current_target}&mode=process" method="post" name="process_user_form">
<input type="hidden" name="mode" value="process" />
<input type="hidden" name="action" value="" />

{assign var="pagestr" value="`$navigation.script`&page=`$navigation.page`"}

<table class="table table-striped dataTable vertical-center" width="100%">
<thead>
<tr class='sort_order'>
{if $accl.$page_acl}
	<th><input type='checkbox' class='select_all' class_to_select='process_user_item' /></th>
{/if}
    <th>#ID</th>
    <th>{if $search_prefilled.sort_field eq "email"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="{$pagestr}&amp;sort=email&amp;sort_direction={$search_prefilled.sort_direction}">{$lng.lbl_email}</a></th>
    <th>{if $search_prefilled.sort_field eq "name"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="{$pagestr|amp}&amp;sort=name&amp;sort_direction={$search_prefilled.sort_direction}">{$lng.lbl_name}</a></th>
	<th>{if $search_prefilled.sort_field eq "phone"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="{$pagestr|amp}&amp;sort=phone&amp;sort_direction={$search_prefilled.sort_direction}">{$lng.lbl_phone}</a></th>
	<th>{if $search_prefilled.sort_field eq "zipcode"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="{$pagestr|amp}&amp;sort=zipcode&amp;sort_direction={$search_prefilled.sort_direction}">{$lng.lbl_zipcode}</a></th>
	<th>{if $search_prefilled.sort_field eq "last_login"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="{$pagestr|amp}&amp;sort=last_login&amp;sort_direction={$search_prefilled.sort_direction}">{$lng.lbl_last_logged_in}</a></th>
    <th>{$lng.lbl_orders}</th>
    {include file="main/users/search_results_columns.tpl"}
</tr>
</thead>

{foreach from=$users item=user}
<tr{cycle values=', class="cycle"'}>
{if $accl.$page_acl}
	<td width="5"><input type="checkbox" name="user[{$user.customer_id}]"{if $user.customer_id eq $customer_id} disabled="disabled"{/if} class="process_user_item" /></td>
{/if}
	<td align="center"><a href="index.php?target={$current_target}&mode=modify&user={$user.customer_id}" title="{$lng.lbl_modify_profile|escape}">{$user.customer_id}</a></td>
    <td width="200" class="word-break"><a href="index.php?target={$current_target}&mode=modify&user={$user.customer_id}" title="{$lng.lbl_modify_profile|escape}">{$user.email}</a></td>
	<td><a href="index.php?target={$current_target}&mode=modify&user={$user.customer_id}">{$user.firstname} {$user.lastname}</a></td>
    <td>{$user.phone}</td>
    <td>{$user.zipcode}</td>	
	<td nowrap="nowrap">{if $user.last_login ne 0}{$user.last_login|date_format:$config.Appearance.datetime_format}{else}{$lng.lbl_never_logged_in}{/if}</td>
    <td align="center">{if $user.orders}<a href="javascript:void(0); " onClick="document.getElementById('cid').value = '{$user.customer_id}'; document.search_by_cid.submit();">{$user.orders}</a>{/if}</td>

    {include file="main/users/search_results_columns.tpl" user=$user}
</tr>

{/foreach}
</table>
{include file='common/navigation.tpl'}

{if $current_area eq 'A'}
<div class="buttons">
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_delete_selected onclick="javascript: document.forms.process_user_form.action.value='delete'; submitFormAjax('process_user_form');" acl=$page_acl style="btn-danger push-5-r push-20"}
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_export_email_addresses  href="index.php?target=`$current_target`&mode=search&action=export_emails" acl=$page_acl style="btn-green push-5-r push-20"}
</div>
{/if}

</form>
<div id='delete_confirm' width='400'></div>

{* This form links to order search page by specific user *}
<form action="index.php?target=docs_O&sort=date" name='search_by_cid' method='POST'>
    <input type='hidden' name='posted_data[customer][substring]' id='cid' />
    <input type='hidden' name='posted_data[customer][by_customer_id]' value='1' />
    <input type="hidden" value="search" name="action" />
    <input type="hidden" name="search_sections[tab_search_orders_advanced]" value='1' />
</form>

{/if}
</div>

{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_search_results}
{/if}
