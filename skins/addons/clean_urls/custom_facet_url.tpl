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
		<table width="100%">
			<tr>
				<td class="td_l">
					<label>
						<a href="index.php?target=attributes&mode=att&attribute_id={$option.attribute_id}" target="_blank">{$option.name}
					</label>
				</td>
				<td class="td_r">
{*
					<div class="attribute_item">
						<label class="checkbox">
							<input type="radio" class="attribute_option" name="attribute_option[{$option.attribute_id}]" value="0" {if !$option.selected}checked=""{/if} onclick="get_attribute_option();" />{$lng.lbl_none}&nbsp;
						</label>
					</div>
*}
					{foreach from=$option.options item=v}
					<div class="attribute_item">
						<label class="checkbox">
							<input type="hidden" id="attribute_clean_url_{$v.attribute_value_id}" value="{$v.clean_url}" />
							<input type="checkbox" class="attribute_option" name="attribute_option[{$option.attribute_id}][]" rel="{$option.attribute_id}" value="{$v.attribute_value_id}" onclick="get_attribute_option();" {if $v.checked}checked=""{/if} /><span id="attribute_option_name_{$v.attribute_value_id}">{$v.name}</span>&nbsp;
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
    <td width="20%"><strong>{$lng.lbl_title}:</strong></td>
    <td><input type="text" name="title" value="{$title}" /></td>
</tr>
<tr>
    <td width="20%"><strong>{$lng.lbl_clean_urls_combination}:</strong></td>
    <td><span id="clean_urls_combination"></span></td>
</tr>
<tr>
    <td width="20%"><strong>{$lng.lbl_options_combination}:</strong></td>
    <td><span id="options_combination"></span></td>
</tr>
<tr id="custom_facet_url_tr">
    <td width="20%"><strong>{$lng.lbl_custom_clean_url}:</strong></td>
    <td><input type="text" name="custom_facet_url" id="custom_facet_url" value="{$custom_facet_url}" size="40"></td>
</tr>
<tr>
    <td width="20%"><strong>{$lng.lbl_description}:</strong></td>
    <td><textarea name="description">{$description}</textarea></td>
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
var options_combination = [];

{literal}
function get_attribute_option() {
    var clean_urls_combination = [];
    var clean_urls_val = [];
    options_combination = [];
    var attribute_value_ids = [];

    var selected_attr_options = [];
    var cnt_attr_options = [];

    $(".attribute_option:checked").each(function(index) {
        if ($(this).val() != 0) {
            var attribute_id = $(this).attr('rel');
            if (selected_attr_options[attribute_id] == null) {
                selected_attr_options[attribute_id] = [];
            }
            selected_attr_options[attribute_id].push($(this).val()); 
        }  
    });

    var dbglog = '';
    var first_cnt = true;
    for (var a_id in selected_attr_options) {
        if (first_cnt) {
            cnt_attr_options[a_id] = -1;
            first_cnt = false; 
        } else {
             cnt_attr_options[a_id] = 0; 
        } 
/*
        for (var idx in selected_attr_options[a_id]) {
            dbglog = dbglog + a_id + ': ' + selected_attr_options[a_id][idx] + ' cnt: ' + cnt_attr_options[a_id] +'\n';
        }
*/
    }
    //alert(dbglog);

    var is_end = false;

    var safety_cnt = 1000;

    do {

        is_end = false;
        var options = [];
        for (var a_id in selected_attr_options) {  
            var option_id = 0;
            if (!is_end) {
                if (cnt_attr_options[a_id] >= selected_attr_options[a_id].length-1) {
                    cnt_attr_options[a_id] = 0;
                } else {
                    cnt_attr_options[a_id]++;
                    is_end = true;
                }
            }
            option_id = selected_attr_options[a_id][cnt_attr_options[a_id]];

            if (option_id == 0)
                continue;

            options.push(option_id); 
        }

        if (!is_end || (options.length == 0))
            break;
        
        var options_combination_str = []; 
        var clean_urls_combination_str = [];
        for (var o in options) {
            options_combination_str.push($('#attribute_option_name_' + options[o]).text());
            clean_urls_combination_str.push($('#attribute_clean_url_' + options[o]).val());
        }    
        options_combination.push(options_combination_str.join(' | '));
        attribute_value_ids.push(options.join(','));
        clean_urls_combination.push(clean_urls_combination_str.join('/') + '/');
        clean_urls_val.push(clean_urls_combination_str.join('/'));

        safety_cnt--;
    } while (is_end && safety_cnt > 0 ); 
    
    if (options_combination.length) { 
        $('#options_combination').html(options_combination.join('<br>'));
        $('#attribute_value_ids').val(attribute_value_ids.join('###'));
        $('#clean_urls_combination').html(clean_urls_combination.join('<br>'));
        $('#clean_urls').val(clean_urls_val.join('###'));
    } else {
        $('#options_combination').html("");
        $('#clean_urls_combination').text(""); 
    }

    if (options_combination.length > 1) { 
        $('#custom_facet_url_tr').hide();
    } else {
        $('#custom_facet_url_tr').show();
    }
}


function _submit_form() {
	/*if ($('#custom_facet_url').val() == "" && options_combination.length == 1) {
		alert(txt_fill_custom_clean_url_field);
	} else */
    if ($('#attribute_value_ids').val() == "") {
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
