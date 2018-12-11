<div class="dialog_title">{$lng.txt_advertising_campaigns_note}</div>
{if $campaigns}
{capture name=section}
<table width="100%" class="header">
<tr>
	<th>{$lng.lbl_campaign}</th>
	<th>{$lng.lbl_usage_type}</th>
	<th>&nbsp;</th>
</tr>
{foreach from=$campaigns item=v}
<tr>
	<td><a href="index.php?target=salesman_adv_campaigns&campaign_id={$v.campaign_id}">{$v.campaign}</a></td>
	<td>{if $v.type eq 'G'}{$lng.lbl_get_parameter}{elseif $v.type eq 'R'}{$lng.lbl_http_referer}{else}{$lng.lbl_landing_page}{/if}</td>
	<td>
        {include file='buttons/button.tpl' button_title=$lng.lbl_delete href="index.php?target=salesman_adv_campaigns&action=delete&amp;campaign_id=`$v.campaign_id`" acl='__1109'}
    </td>
</tr>
{/foreach}
</table>
{/capture}
{include file="common/section.tpl" content=$smarty.capture.section title=$lng.lbl_advertising_campaigns}
{/if}

{capture name=section}
<form action="index.php?target=salesman_adv_campaigns" method="post" name="campaigns_form">
<input type="hidden" name="action" value="add" />
<input type="hidden" name="campaign_id" value="{$campaign.campaign_id}" />

<div class="input_field_0">
	<label>{$lng.lbl_campaign_name}</label>
	<input type="text" name="add[campaign]" value="{$campaign.campaign}" />
</div>
<div class="input_field_0">
    <label>{$lng.lbl_pay_per_visit}</label>
    <input type="text" size="5" name="add[per_visit]" value="{$campaign.per_visit|formatprice|default:$zero}" />
</div>
<div class="input_field_0">
    <label>{$lng.lbl_pay_per_period}</label>
    <input type="text" size="5" name="add[per_period]" value="{$campaign.per_period|formatprice|default:$zero}" />
</div>
<div class="input_field_0">
	<label>{$lng.lbl_date}</label>
    {include file='main/select/date.tpl' name='posted_data[start_period]' value=$campaign.start_period} -
    {include file='main/select/date.tpl' name='posted_data[end_period]' value=$campaign.end_period}
</div>
<div class="input_field_0">
    <label>{$lng.lbl_usage_type}</label>
    <select name="add[type]" onchange="javascript: change_textarea(this.value);">
	<option value='G'{if $campaign.type eq 'G' || $campaign.type eq ''} selected="selected"{/if}>{$lng.lbl_get_parameter}</option>
    <option value='R'{if $campaign.type eq 'R'} selected="selected"{/if}>{$lng.lbl_http_referer}</option>
    <option value='L'{if $campaign.type eq 'L'} selected="selected"{/if}>{$lng.lbl_landing_page}</option>
	</select>
</div>
<div class="input_field_0">
    <label>&nbsp;</label>
    {$lng.txt_acm_general_note}<br /><br />
    <textarea id="textarea" name="add[data]" rows="3" cols="50">{$campaign.data|escape}</textarea><br />{if $campaign.type eq 'L'}<br /><b>{$lng.lbl_img_tag}</b><br /><input type="text" readonly="readonly" value="&lt;IMG src=&quot;{$current_location}/adv_counter.php?campaign_id={$v.campaign_id}&quot; border=&quot;0&quot; width=&quot;1&quot; height=&quot;1&quot;&gt;" size="50" />{/if}
</div>

<script type="text/javascript" language="JavaScript 1.2">
<!--
change_textarea('{$campaign.type|default:"G"}');
-->
</script>

{include file='buttons/button.tpl' button_title=$lng.lbl_save href="javascript: cw_submit_form('campaigns_form')" acl='__1109'}
{if $campaign.campaign_id}
{include file='buttons/button.tpl' button_title=$lng.lbl_close href="javascript: cw_submit_form('campaigns_form, 'close')" acl='__1109'}
{/if}

</form>
{/capture}
{if $campaign.campaign_id > 0}{assign var="dialog_title" value=$lng.lbl_modify_advertising_campaigns}{else}{assign var="dialog_title" value=$lng.lbl_add_advertising_campaigns}{/if} 
{include file="common/section.tpl" content=$smarty.capture.section title=$dialog_title}
