{if $mode eq 'add'}
	{include file='common/page_title.tpl' title=$lng.lbl_add_custom_facet_url}
{else}
	{include file='common/page_title.tpl' title="`$lng.lbl_custom_facet_url` \"`$custom_facet_url`\""}
{/if}

<div class="clear"></div>

<div class="box">
	<div class="subheader">
		<span class="subheader_left">{$lng.lbl_attributes_options}</span>
	</div>
	<div class="clear"></div>
	{foreach from=$attributes_options item=option}
	<div class="input_field_0" >
		<table>
			<tr>
				<td class="td_l">
					<label>
						<a href="index.php?target=attributes&mode=att&attribute_id={$option.attribute_id}" target="_blank">{$option.name}
					</label>
				</td>
				<td class="td_r">
					<div class="attribute_item">
						<label class="checkbox">
							<input type="radio" class="attribute_option" name="attribute_option[{$option.attribute_id}]" value="0" {if !$option.selected}checked=""{/if} onclick="get_attribute_option();" />{$lng.lbl_none}&nbsp;
						</label>
					</div>
					{foreach from=$option.options item=v}
					<div class="attribute_item">
						<label class="checkbox">
							<input type="hidden" id="attribute_clean_url_{$v.attribute_value_id}" value="{$v.clean_url}" />
							<input type="radio" class="attribute_option" name="attribute_option[{$option.attribute_id}]" value="{$v.attribute_value_id}" onclick="get_attribute_option();" {if $v.checked}checked=""{/if} /><span id="attribute_option_name_{$v.attribute_value_id}">{$v.name}</span>&nbsp;
						</label>
					</div>
					{/foreach}
				</td>
			</tr>
		</table>
	</div>
	{/foreach}
</div>
<div class="clear"></div>

<div class="box">
<form action="index.php?target={$current_target}&mode=add&custom_facet_url_id={$custom_facet_url_id}" method="post" name="custom_facet_url_form">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="attribute_value_ids" id="attribute_value_ids" value="" />
<input type="hidden" name="clean_urls" id="clean_urls" value="" />

<table width="100%" class="header">
<tr>
    <td width="20%"><strong>{$lng.lbl_clean_urls_combination}:</strong></td>
    <td><span id="clean_urls_combination"></span></td>
</tr>
<tr>
    <td width="20%"><strong>{$lng.lbl_options_combination}:</strong></td>
    <td><span id="options_combination"></span></td>
</tr>
<tr>
    <td width="20%"><strong>{$lng.lbl_custom_clean_url}:</strong></td>
    <td><input type="text" name="custom_facet_url" id="custom_facet_url" value="{$custom_facet_url}" size="40"></td>
</tr>
<tr>
    <td width="20%"><strong>{$lng.lbl_image}:</strong></td>
    <td>
		{include file="main/images/edit.tpl" image=$custom_facet_image delete_js="cw_submit_form('custom_facet_url_form', 'delete_image');" button_name=$lng.lbl_browse in_type="facet_categories_images"}
	</td>
</tr>
</table>
</form>
</div>

{include file='buttons/button.tpl' button_title=$lng.lbl_save href="javascript:_submit_form();" acl='__1201'}
{if $mode eq 'details'}
	{include file='buttons/button.tpl' button_title=$lng.lbl_add_new href="index.php?target=custom_facet_urls&mode=add"}
{/if}

<script type="text/javascript">
var txt_select_attributes_options = "{$lng.txt_select_attributes_options}";
var txt_fill_custom_clean_url_field = '{$lng.txt_fill_custom_clean_url_field}';
{literal}
function get_attribute_option() {
	var clean_urls_combination = [];
	var options_combination = [];
	var attribute_value_ids = [];

	$(".attribute_option:checked").each(function(index) {
		if ($(this).val() != 0) {
			var attribute_value_id = $(this).val();
			clean_urls_combination.push($('#attribute_clean_url_' + attribute_value_id).val());
			options_combination.push($('#attribute_option_name_' + attribute_value_id).text());
			attribute_value_ids.push(attribute_value_id);
		}
	});

	if (attribute_value_ids.length > 0) {
		$('#attribute_value_ids').val(attribute_value_ids.join(','));
		$('#clean_urls_combination').text(clean_urls_combination.join('/') + '/');
		$('#clean_urls').val(clean_urls_combination.join('/'));
		$('#options_combination').text(options_combination.join(' | '));
	} else {
		$('#clean_urls_combination').text("");
		$('#options_combination').text("");
	}
}

function _submit_form() {
	if ($('#custom_facet_url').val() == "") {
		alert(txt_fill_custom_clean_url_field);
	} else if ($('#attribute_value_ids').val() == "") {
		alert(txt_select_attributes_options);
	} else {
		cw_submit_form('custom_facet_url_form');
	}
}

$(document).ready(function() {
	get_attribute_option();
});
{/literal}
</script>
