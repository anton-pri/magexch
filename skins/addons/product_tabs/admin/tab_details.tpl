{capture name=section}
<table width="100%" border="0">
<tr>
	<td><a href="index.php?target={$current_target}&amp;mode={$mode}&amp;product_id={$product_id}&amp;js_tab=product_tabs" title="{$lng.lbl_pt_tabs_list}">{$lng.lbl_pt_tabs_list}</a> :: {$tab_data.title|escape}</td>
</tr>
<tr>
	<td><img src="{$ImagesDir}/spacer.gif" height="10px" class="Spc" alt="" /></td>
</tr>
</table>

<form action="index.php?target={$current_target}" method="post" name="tabs_details">
<input type="hidden" name="mode" value="{$mode}" />
<input type="hidden" name="action" value="tabs_modify" />
<input type="hidden" name="tab_type" value="{$tab_type}" />
<input type="hidden" name="product_id" value="{$product.product_id}" />
<input type="hidden" name="tab_id" value="{$tab_data.tab_id}" />


{include file='common/subheader.tpl' title=$lng.lbl_pt_details}

<div class="input_field_1">
	<label class='multilan'>
        {$lng.lbl_pt_tab_title} 
    </label>
	<input type="text" size="50" maxlength="255" name="tab_data[title]" value="{$tab_data.title|default:$lng.lbl_pt_unknown|escape}"{if $read_only} disabled{/if} />
</div>

<div class="input_field_0">
    <label>
        {$lng.lbl_pt_tab_order}
    </label>
	<input type="text" size="6" maxlength="11" name="tab_data[number]" value="{$tab_data.number|default:0|escape}"{if $read_only} disabled{/if} />
</div>

<div class="input_field_0">
    <label>
        {$lng.lbl_pt_tab_parse}
    </label>
	<input type="checkbox" name="tab_data[parse]" value="Y"{if $tab_data.parse eq 1} checked{/if} />
</div>

<div class="input_field_0">
    <label>
        {$lng.lbl_pt_tab_active}
    </label>
	<input type="checkbox" name="tab_data[active]" value="Y"{if $tab_data.active eq 1} checked{/if} />
</div>

{if $attributes}
<div class="input_field_0">
    <label>{$lng.lbl_attributes}</label>
	{include file='main/select/attribute.tpl' name="tab_data[attributes][]" value=$tab_data.attributes multiple=5 is_show=1}
</div>
{/if}

<div class="input_field_1">
    <label class='multilan'>
        {$lng.lbl_pt_tab_content} 
    </label>
	{include file='main/textarea.tpl' name="tab_data[content]" data="`$tab_data.content`" init_mode='exact'}
</div>


{include file='buttons/button.tpl' href="javascript:cw_submit_form('tabs_details');" button_title=$lng.lbl_pt_button_save}</form>

{/capture}
{include file='admin/wrappers/block.tpl' title=$lng.lbl_pt_product_tabs content=$smarty.capture.section}
