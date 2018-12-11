{capture name="main_section"}
{capture name=section}
<form action="index.php?target={$current_target}" method="post" name="profile_options">
{*if $smarty.get.user_type eq "A"}{include file='common/page_title.tpl' title=$lng.lbl_profile_options_admin}{else}{include file='common/page_title.tpl' title=$lng.lbl_profile_options_customer}{/if*}

<div class="box">

<input type="hidden" name="user_type" value="{$user_type}" />
<input type="hidden" name="action" value="update_status" />
<table class="header profile_options">
<tr>
	<th rowspan="2" width="160">{$lng.lbl_field_name}</td>
{foreach from=$usertypes_array item=to_disable key=utype}
	<th id="{$utype}{get_column_counter usertype=$utype}">
{assign var="def_usertype" value=$utype|truncate:'1':''}
{lng name="lbl_user_`$def_usertype`"}
{if $def_usertype eq 'R'}
    {if $membership_titles.$utype}({$membership_titles.$utype}){/if}
{elseif $def_usertype eq 'C'}
    {if $membership_titles.$utype}({$membership_titles.$utype}){/if}
{/if}
	</th>
{/foreach}
</tr>

<tr>
{foreach from=$usertypes_array item=to_disable key=utype}
	<th align="center">{$lng.lbl_moderator} - {$lng.lbl_self}<br/>({$lng.lbl_active} / {$lng.lbl_required})</th>
{/foreach}
</tr>

{math equation="x*2+1" x=$usertypes_array_count assign="colspan"}

{foreach from=$profile_sections item=section key=section_name}
<tr {cycle values=" class='cycle',"}>
	<td colspan="{$colspan}">
    {capture name=vs_box}
    {include file='main/visiblebox_link.tpl' mark="open_close_section_`$section_name`"}
    {/capture}
    {include file='common/subheader.tpl' title=$section.section_title class="grey" right=$smarty.capture.vs_box}
    </td>
</tr>
<tbody id="open_close_section_{$section_name}" style="display: none">
{if $profile_fields.$section_name}
{foreach from=$profile_fields.$section_name item=field}
<tr {cycle values=", class='cycle'"}>
    <td>
    {$field.title}
    <input type="hidden" name="upd_flag[{$field.field_id}]" value="1" />
    </td>
{foreach from=$usertypes_array item=to_disable key=utype}
{if $field.is_protected}
{assign var="to_disable" value=1}
{/if}
    <td nowrap align="center">
{if $field.areas.$utype.is_disabled}
<input type="hidden" name="upd[{$field.field_id}][{$utype}][r]" value="0"/>
<input type="hidden" name="upd[{$field.field_id}][{$utype}][a]" value="0"/>
<input type="hidden" name="upd[{$field.field_id}][{$stype}][r]" value="0"/>
<input type="hidden" name="upd[{$field.field_id}][{$stype}][a]" value="0"/>
{else}
    {assign var="def_usertype" value=$utype|truncate:'1':''}
    {assign var='stype' value="#`$utype`"}
    {assign var="sdef_usertype" value="#`$utype`"}
    <input type="hidden" name="upd[{$field.field_id}][{$utype}][a]" value="0"/>
    <input type="hidden" name="upd[{$field.field_id}][{$utype}][r]" value="0"/>
    <input type="checkbox" id="a{$field.field_id}{$utype}" title='{$lng.lbl_active} for {$lng.lbl_moderator}' {if $to_disable eq ''} name="upd[{$field.field_id}][{$utype}][a]"{/if}{if $field.areas.$utype.is_avail || $is_avail} checked{/if}{if $to_disable} disabled{/if} value="1" onclick="fca(this, '{$field.field_id}', '{$utype}', '{$def_usertype}')"/> 
    / 
    <input type="checkbox" id="r{$field.field_id}{$utype}" title='{$lng.lbl_required} for {$lng.lbl_moderator}'{if $to_disable eq ''} name="upd[{$field.field_id}][{$utype}][r]"{/if}{if $field.areas.$utype.is_required} checked{/if}{if $to_disable || !$field.areas.$utype.is_avail} disabled{/if} value="1" onclick="fcr(this, '{$field.field_id}', '{$utype}', '{$def_usertype}')"/>
    &nbsp;-&nbsp;
    <input type="checkbox" id="a{$field.field_id}{$stype}" title='{$lng.lbl_active} for {$lng.lbl_self}'{if $to_disable eq ''} name="upd[{$field.field_id}][{$stype}][a]"{/if}{if $field.areas.$stype.is_avail || $is_avail} checked{/if}{if $to_disable} disabled{/if} value="1" onclick="fca(this, '{$field.field_id}', '{$stype}', '{$sdef_usertype}')"/> 
    / 
    <input type="checkbox" id="r{$field.field_id}{$stype}" title='{$lng.lbl_required} for {$lng.lbl_self}'{if !$to_disable} name="upd[{$field.field_id}][{$stype}][r]"{/if}{if $field.areas.$stype.is_required} checked{/if}{if $to_disable || !$field.areas.$stype.is_avail} disabled="disabled"{/if} value="1" onclick="fcr(this, '{$field.field_id}', '{$stype}', '{$sdef_usertype}')"/>
    {if $to_disable}
{if $field.areas.$utype.is_required}<input type="hidden" name="upd[{$field.field_id}][{$utype}][r]" value="1"/>{/if}
{if $field.areas.$utype.is_avail}<input type="hidden" name="upd[{$field.field_id}][{$utype}][a]" value="1"/>{/if}
{if $field.areas.$stype.is_required}<input type="hidden" name="upd[{$field.field_id}][{$stype}][r]" value="1"/>{/if}
{if $field.areas.$stype.is_avail}<input type="hidden" name="upd[{$field.field_id}][{$stype}][a]" value="1"/>{/if}
    {/if}
{/if}
    </td>
{/foreach}
</tr>
{/foreach}
{/if}
</tbody>

{/foreach}
</table>
</div>
<div class="buttons">
    {include file='admin/buttons/button.tpl' href="javascript: cw_submit_form('profile_options');" button_title=$lng.lbl_save acl='__2502' style="btn-green push-20 push-5-r"}
</div>

</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.section title=$lng.lbl_add_new_language}

{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.main_section title=$lng.lbl_profile_options}

<script language="Javascript">
var membership_counter = new Array();
var rel_membership = new Array();
{foreach from=$usertypes_array item=to_disable key=utype}
membership_counter['{$utype}'] = parseInt('{$columns_counter.$utype}');
rel_membership['{$utype}'] = '{$utype|truncate:'1':''}';
membership_counter['#{$utype}'] = parseInt('{$columns_counter.$utype}');
rel_membership['#{$utype}'] = '#{$utype|truncate:'1':''}';
{/foreach}

{literal}

function fca(el, field_id, utype, dtype) {
    req = document.getElementById('r'+field_id+utype);
    if (req) req.disabled = !el.checked;
    if (utype == dtype) {
        for(i in rel_membership) {
            if (rel_membership[i] == dtype) {
                document.getElementById('a'+field_id+i).checked = el.checked;
                document.getElementById('r'+field_id+i).disabled = !el.checked;
            }
        }
    }
}

function fcr(el, field_id, utype, dtype) {
    if (utype == dtype) {
        for(i in rel_membership) {
            if (rel_membership[i] == dtype)
                document.getElementById('r'+field_id+i).checked = el.checked;
        }
    }
}

function switchMembership(utype, img) {
    if (img.alt == '1') {
        img.src = images_dir+'/plus.gif';
        disp = 'none';
        img.alt = '0';
    }
    else {
        img.src = images_dir+'/minus.gif';
        disp = '';
        img.alt = '1';
    }

    for(i in rel_membership) {
        if (rel_membership[i] == utype && i != utype)
            cw_show_membership(i, disp);
    }
}
{/literal}

</script>
