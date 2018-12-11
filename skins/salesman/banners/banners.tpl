<div class="dialog_title">{$lng.txt_banner_html_code_note}</div>

<p align="justify">
{$lng.txt_banner_html_code_comment}
</p>

{capture name=section}
{if $banners}
{foreach from=$banners item=v}
<div class="input_field_1">
	<label>{$v.banner}</label>
	{include file='main/banners/display_banner.tpl' banner=$v type=$local_type salesman=$customer_id assign='ban'}{$ban|amp}
</div>
<div class="input_field_1">
	<label>{$lng.lbl_iframe_code}:</label>
    <textarea cols="60" rows="5" readonly="readonly">{include file="main/banners/display_banner.tpl" assign="ban" banner=$v type="iframe" salesman=$customer_id current_location=$http_location}{$ban|escape}</textarea>
</div>
{if $v.banner_type eq 'G'}
<div class="input_field_1">
	<label>{$lng.lbl_html_code}:</label>
	<textarea cols="60" rows="5" readonly="readonly">{include file="main/banners/display_banner.tpl" assign="ban" banner=$v type="js" salesman=$customer_id current_location=$http_location}{$ban|escape}</textarea>
</div>
{else}
<div class="input_field_1">
    <label>$lng.lbl_javascript_version}:</label>
    <textarea cols="35" rows="5" readonly="readonly">{include file="main/banners/display_banner.tpl" assign="ban" banner=$v type="js" salesman=$customer_id current_location=$http_location}{$ban|escape}</textarea>
</div>
<div class="input_field_1">
	<label>{$lng.lbl_ssi_version}:</label>
    <textarea cols="35" rows="5" readonly="readonly">{include file="main/banners/display_banner.tpl" assign="ban" banner=$v type="ssi" salesman=$customer_id current_location=$http_location}{$ban|escape}</textarea>
</div>
{/if}
{/foreach}

{else}
<div align="center">{$lng.lbl_no_banners_have_been_defined}</div>
{/if}
{/capture}
{include file='common/section.tpl' content=$smarty.capture.section title=$lng.lbl_available_banners}
