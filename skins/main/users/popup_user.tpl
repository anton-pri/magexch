<script language="javascript">
{literal}
function set_user() {
    value = document.results_form.customer_id.value;
    if (value) {
{/literal}
        window.opener.document.getElementById('{$element_id}').value=value;
        cw_submit_form(window.opener.document.{$target_form});
        window.close();
{literal}
    }
}

$(document).ready(function() {
    function toggle_active_section() {
        var id = $(this).attr('id')+'_section';
        if ($(this).attr('checked')) {
            $('#'+id).show();
        } else {
            $('#'+id).hide();
        }
    };

    $('#active_sections').find('input').each(toggle_active_section);
    $('#active_sections').find('input').bind('click',toggle_active_section);
});
{/literal}
</script>

{*jstabs}
default_tab={$js_tab|default:"basic_search"}
default_template="main/users/search_form.tpl"

[submit]
title="{$lng.lbl_search}"
href="javascript: cw_submit_form(document.search_form);"

[reset]
title="{$lng.lbl_reset}"
href="javascript: cw_submit_form(document.search_form, 'reset');"

[basic_search]
{assign var='lbl' value="lbl_search_user_`$current_search_type`"}
title="{$lng.$lbl}"

[adv_search_address]
title={$lng.lbl_search_customer_by_address}

[adv_search_admin]
title={$lng.lbl_adv_customer_search_admin}

[adv_search_orders]
title={$lng.lbl_orders}

{/jstabs*}
{capture name=section}

<form name="search_form" action="index.php?target={$current_target}" method="post">
<input type="hidden" name="action" value="search" />
<input type="hidden" name="js_tab" id="form_js_tab" value="">

    <div class='box right' id='active_sections'>
        <span>{$lng.lbl_additional_criteria}:</span>
        &nbsp;
        <label><input type='checkbox' value='1' name='search_sections[adv_search_address]' id='adv_search_address' {if $search_prefilled.search_sections.adv_search_address} checked="checked"{/if} /> {$lng.lbl_search_customer_by_address}</label>
        &nbsp;&nbsp;
        <label><input type='checkbox' value='1' name='search_sections[adv_search_admin]' id='adv_search_admin' {if $search_prefilled.search_sections.adv_search_admin} checked="checked"{/if} /> {$lng.lbl_adv_customer_search_admin}</label>
        &nbsp;&nbsp;
        <label><input type='checkbox' value='1' name='search_sections[adv_search_orders]' id='adv_search_orders' {if $search_prefilled.search_sections.adv_search_orders} checked="checked"{/if} /> {$lng.lbl_orders}</label>
    </div>

    {include file='main/users/search_form.tpl' included_tab='basic_search'}
    {include file='main/users/search_form.tpl' included_tab='adv_search_address'}
    {include file='main/users/search_form.tpl' included_tab='adv_search_admin'}
    {include file='main/users/search_form.tpl' included_tab='adv_search_orders'}

<div class="buttons">
    {include file='buttons/button.tpl' button_title=$lng.lbl_search href="javascript: cw_submit_form('search_form');" style="button"}
    {include file='buttons/button.tpl' button_title=$lng.lbl_reset href="javascript: cw_submit_form('search_form', 'reset');" style="button"}
</div>

{*include file='tabs/js_tabs.tpl' is_checkboxes=1 name="search_sections" value=$search_prefilled.search_sections*}
</form>



{/capture}
{include file='common/section.tpl' title=$lng.lbl_find_user content=$smarty.capture.section style="hide"}

{if $mode eq 'search'}
{include file='common/navigation_counter.tpl'}
{/if}


{if $mode eq 'search' and $users}
{include file='common/navigation.tpl'}

{capture name=list}

<script type="text/javascript">
{literal}
      $(".hide").css({'display' : 'none'});
{/literal}

      $(".hide").after($('<a class="expand_search right">{$lng.lbl_new_search}</a>'));
{literal}

      $("a.expand_search").click(function () {
           $(".hide").css({'display' : 'block'});
           $("a.expand_search").remove();
      });

{/literal}
</script>

{assign var="pagestr" value="`$navigation.script`&page=`$navigation_page`"}
<form name="results_form" action="index.php?target={$current_target}" method="post">
<input type="hidden" name="customer_id" value="0">

<table class="header users" width="100%">
<tr>
    <th>&nbsp;</th>
    <th>#ID</th>
    <th class="user_mail">{if $search_prefilled.sort_field eq "email"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="{$pagestr}&amp;sort=email">{$lng.lbl_email}</a></th>
    <th class="user_name">{if $search_prefilled.sort_field eq "name"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="{$pagestr|amp}&amp;sort=name">{$lng.lbl_name}</a></th>
    <th>{if $search_prefilled.sort_field eq "last_login"}{include file="buttons/sort_pointer.tpl" dir=$search_prefilled.sort_direction}&nbsp;{/if}<a href="{$pagestr|amp}&amp;sort=last_login">{$lng.lbl_last_logged_in}</a></th>
</tr>

{foreach from=$users item=user}
<tr{cycle values=', class="cycle"'}>
    <td width="5"><input type="radio" name="customer_id_radio" value="{$user.customer_id}"{if $user.customer_id eq $customer_id} disabled="disabled"{/if} onchange="javascript:document.results_form.customer_id.value=this.value"/></td>
    <td>{$user.customer_id}</td>
    <td class="user_mail">{$user.email}</td>
    <td class="user_name">{$user.customer_id|user_title:$user.usertype}</td>
    <td nowrap="nowrap">{if $user.last_login ne 0}{$user.last_login|date_format:$config.Appearance.datetime_format}{else}{$lng.lbl_never_logged_in}{/if}</td>
</tr>
{/foreach}

</table>
<br />
<div id="sticky_content">
{include file='buttons/button.tpl' button_title=$lng.lbl_set_account href="javascript:set_user();"}
</div>
</form>

{/capture}
{include file='common/section.tpl' title=$lng.lbl_search_results content=$smarty.capture.list}

{include file='common/navigation.tpl'}

{/if}

