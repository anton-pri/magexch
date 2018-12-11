<div class="dialog_title">{$lng.txt_banners_note}</div>

{if $banners}
{capture name=section}
{foreach from=$banners item=v}
<div class="section">
	<b>{$lng.lbl_banner}:</b>&nbsp;{$v.banner}&nbsp;<i>({if $v.banner_type eq 'T'}{$lng.lbl_text_link}{elseif $v.banner_type eq 'G'}{$lng.lbl_graphic_banner}{elseif $v.banner_type eq 'M'}{$lng.lbl_media_rich_banner}{else}{$lng.lbl_product_banner}{/if})</i><br /><br />
    <div class="bordered">
	{include file='main/banners/display_banner.tpl' assign="ban" banner=$v type='iframe' salesman=''}{$ban|amp}
    </div>

    {include file='buttons/button.tpl' href="index.php?target=salesman_banners&banner_id=`$v.banner_id`&action=delete" button_title=$lng.lbl_delete acl='__1106'}
    {include file='buttons/button.tpl' href="index.php?target=salesman_banners&banner_id=`$v.banner_id`" button_title=$lng.lbl_modify acl='__1106' acl='__1106'}
</div>
{/foreach}
{/capture}
{include file='common/section.tpl' content=$smarty.capture.section title=$lng.lbl_available_banners}
{/if}

{if $accl.__06}
{capture name=section}
<form action="index.php?target=salesman_banners" method="post" enctype="multipart/form-data" name="save_banner_form">
<input type="hidden" name="action" value="add" />
<input type="hidden" name="banner_id" value="{$banner.banner_id}" />

<div class="input_field_0">
    <label>{$lng.lbl_banner_name}</label>
    <input type="text" maxlength="128" size="40" name="add[banner]" value="{$banner.banner}" />
</div>
<div class="input_field_0">
    <label>{$lng.lbl_banner_type}</label>
    {include file='main/select/banner_type.tpl' name='add[banner_type]' value=$banner.banner_type}
</div>
<div class="input_field_0">
    <label>{$lng.lbl_banner_width}</label>
    <input type="text" size="5" value="{$banner.banner_x|default:100}" name="add[banner_x]" />
</div>
<div class="input_field_0">
    <label>{$lng.lbl_banner_height}</label>
    <input type="text" size="5" value="{$banner.banner_y|default:100}" name="add[banner_y]" />
</div>
<div class="input_field_0">
    <label>{$lng.lbl_availability}</label>
    <input type="checkbox" value="Y" name="add[avail]"{if $banner.avail eq 'Y' || $banner.banner_id eq ''} checked="checked"{/if} />
</div>
<div class="input_field_0">
    <label>{$lng.lbl_open_in_new_window}</label>
    <input type="checkbox" value="Y" name="add[open_blank]"{if $banner.open_blank eq 'Y' || $banner.banner_id eq ''} checked="checked"{/if} />
</div>
{if $banner.banner_type eq 'T'}
<div class="input_field_0">
    <label>{$lng.lbl_text}</label>
    <textarea cols="50" rows="3" name="add[body]">{$banner.body}</textarea>
</div>
{elseif $banner.banner_type eq 'G'}
<div class="input_field_0">
    <label>{$lng.lbl_text} ({$lng.lbl_optional})</label>
    <textarea cols="50" rows="3" name="add[legend]">{$banner.legend}</textarea>
</div> 
<div class="input_field_0">
    <label>{$lng.lbl_alt_tag} ({$lng.lbl_optional})</label> 
    <textarea cols="50" rows="3" name="add[alt]">{$banner.alt}</textarea>
</div>  
<div class="input_field_0">
    <label>{$lng.lbl_text_location}</label>
    <select name="add[direction]">
    <option value="U"{if $banner.direction eq 'U' || $banner.direction eq ''} selected="selected"{/if}>{$lng.lbl_above}</option>
    <option value="L"{if $banner.direction eq 'L'} selected="selected"{/if}>{$lng.lbl_left}</option>
    <option value="R"{if $banner.direction eq 'R'} selected="selected"{/if}>{$lng.lbl_right}</option>
	<option value="D"{if $banner.direction eq 'D'} selected="selected"{/if}>{$lng.lbl_below}</option>
	</select>
</div>  
<div class="input_field_0">
    <label>{$lng.lbl_image}</label> 
    <input type="file" name="userfile" />
</div>  
{elseif $banner.banner_type eq 'P'}
<div class="input_field_0">
    <label>{$lng.lbl_picture}</label>
    <input type="checkbox" name="add[is_image]" value='Y'{if $banner.is_image eq 'Y'} checked="checked"{/if} />
</div>
<div class="input_field_0">
    <label>{$lng.lbl_full_name}</label>
    <input type="checkbox" name="add[is_name]" value='Y'{if $banner.is_name eq 'Y'} checked="checked"{/if} />
</div>  
<div class="input_field_0">
    <label>{$lng.lbl_description}</label>
    <input type="checkbox" name="add[is_descr]" value='Y'{if $banner.is_descr eq 'Y'} checked="checked"{/if} />
</div>
<div class="input_field_0">
    <label>{$lng.lbl_add_to_cart_link}</label>
    <input type="checkbox" name="add[is_add]" value='Y'{if $banner.is_add eq 'Y'} checked="checked"{/if} />
</div>
{elseif $banner.banner_type eq 'M'}
<div class="input_field_0">
<script type="text/javascript" language="JavaScript 1.2">
<!--
{literal}
function preview_body() {
	win = window.open('index.php?target=preview_banner','PREVIEW_POPUP','width=600,height=460,toolbar=no,status=no,scrollbars=yes,resizable=yes,menubar=no,location=no,direction=no');
}
{/literal}
-->
</script>
	<label>{$lng.lbl_body}</label>
    <textarea cols="60" rows="10" name="add[body]" id="banner_body">{$banner.body}</textarea>
</div>
<div class="input_field_0">
    <a href="javascript: void(0);" onclick="javascript: document.getElementById('banner_body').value += '<#A#>';"><img src="{$ImagesDir}/open_a.gif" alt="" /></a>
	<a href="javascript: void(0);" onclick="javascript: document.getElementById('banner_body').value += '<#A#>';">{$lng.lbl_add_link_opening_tag}</a>
    <br/>
	<a href="javascript: void(0);" onclick="javascript: document.getElementById('banner_body').value += '<#/A#>';"><img src="{$ImagesDir}/close_a.gif" alt="" /></a>
	<a href="javascript: void(0);" onclick="javascript: document.getElementById('banner_body').value += '<#/A#>';">{$lng.lbl_add_link_closing_tag}</a>
	<br/>
	<a href="javascript: void(0);" onclick="javascript: preview_body();"><img src="{$ImagesDir}/preview_img.gif" alt="{$lng.lbl_preview|escape}" /></a>
	<a href="javascript: void(0);" onclick="javascript: preview_body();">{$lng.lbl_preview}</a>
</div>
{if $elements ne ''}
<div class="input_field_1">
    <label>{$lng.lbl_media_library}<label>
	<iframe width="100%" height="300" src="{$catalogs.admin}/index.php?target=salesman_element_list"></iframe>
</div>
{/if}
{/if}
{include file='buttons/button.tpl' button_title=$lng.lbl_save_banner href="javascript: javascript:cw_submit_form('save_banner_form')" acl='__1106'}
{if $banner.banner_id > 0}
{include file='buttons/button.tpl' button_title=$lng.lbl_close href="javascript: javascript:cw_submit_form('save_banner_form', 'close')" acl='__1106'}
{/if}
</form>

{if $banner.banner_type eq 'M'}
<b>{$lng.lbl_add_media_object}</b><br />
<form action="index.php?target=salesman_banners" method="post" enctype="multipart/form-data" name="banner_add_form">
<input type="hidden" name="action" value="upload" />
<input type="hidden" name="banner_type" value="{$banner.banner_type}" />
<input type="hidden" name="banner_id" value="{$banner.banner_id}" />

<div class="input_field_0">
	<label>{$lng.lbl_media_object}</label>
	<input type="file" name="userfile" />
    <b>{$lng.txt_flash_note}</b>
</tr>
<div class="input_field_0">
    <label>{$lng.lbl_width}</label>
    <input type="text" size="5" name="width" />
</div>
<div class="input_field_0">
    <label>{$lng.lbl_height}</label>
    <input type="text" size="5" name="height" />
</div>
{include file='buttons/button.tpl' button_title=$lng.lbl_add href="javascript: javascript:cw_submit_form('banner_add_form', 'upload')" acl='__1106'}
</form>
{/if}
{/capture}
{if $banner}{assign var="title" value=$lng.lbl_modify_banner}{else}{assign var="title" value=$lng.lbl_add_banner}{/if}
{include file='common/section.tpl' content=$smarty.capture.section title=$title}
{/if}
