<script type="text/javascript" language="JavaScript 1.2">
<!--
var err_choose_file_first = "{$lng.err_choose_file_first|escape:javascript|strip_tags|replace:"\n":" "|replace:"\r":" "}";
var err_choose_directory_first = "{$lng.err_choose_directory_first|escape:javascript|strip_tags|replace:"\n":" "|replace:"\r":" "}";
var field_filename = "{$field_filename}";
var field_path = "{$field_path}";
{literal}
function setFile (filename, path) {
	if (window.opener) {

		var badfilename_pattern=new RegExp("\.\.\.$");
	    if (badfilename_pattern.test(filename) != false) {
	    	filename = path.replace(/.*[\\/]/g, '');
	    }
		
		if (window.opener.document[field_filename]) {
			window.opener.document[field_filename].value = filename;
            cw_fire_event(window.opener.document[field_filename], "keydown");
        } 

		var obj_filename = window.opener.document.getElementById(field_filename);
		if (obj_filename && window.opener.document[field_filename] == undefined) {
		    obj_filename.value = filename;
            cw_fire_event(obj_filename, "keydown"); 
		}
		
		var obj_filepath = window.opener.document.getElementById(field_path);
		if (obj_filepath && window.opener.document[field_path] == undefined) {
		    obj_filepath.value = path;
            cw_fire_event(obj_filepath, "keydown");
		}
		
		if (window.opener.document[field_path]) {
			window.opener.document[field_path].value = path;
            cw_fire_event(window.opener.document[field_path], "keydown");
        } 
	}
	window.close();
}

function setFileInfo () {
	if (document.files_form && document.files_form.path && document.files_form.path.value != "") {
		setFile(document.files_form.path.options[document.files_form.path.selectedIndex].text, document.files_form.path.value);
	} else {
		alert(err_choose_file_first);
	}
}

function checkDirectory () {
	if (document.dir_form.dir.selectedIndex == -1) {
		alert(err_choose_directory_first);
		return false;
	}
	return true;
}

{/literal}
-->
</script>
<br />
{capture name=section}
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
    <td colspan="2"> 
<form action="index.php?target={$current_target}" method="post" name="upload_file_form" enctype="multipart/form-data">

<input type="hidden" name="dir" value="{$smarty.get.dir|escape:"html"}" />
<input type="hidden" name="field_filename" value="{$smarty.get.field_filename|escape}" />
<input type="hidden" name="field_path" value="{$smarty.get.field_path|escape}" />
<input type="hidden" name="storage_location_code" value="{$storage_location_code}">
    <h2>{$lng.lbl_file_on_local_computer}</h2>
    <div id="file_err"></div>
<div class="warning">{$lng.txt_max_file_size_warning|substitute:"size":$upload_max_filesize}</div>
<table class="images_table">
<tr>
    <td id="on_local_box_0">
        <div ><input id="fileupload" type="file" size="25" name="userfiles[0]" accept="image/*" /></div>
        <div id="progress">
            <div class="bar" style="width: 0%;"> 
                <div class="fileupload-name"></div>
            </div>
        </div>
<input type="button" value="{$lng.lbl_apply|strip_tags:false|escape}" onclick="javascript: cw_submit_form('upload_file_form');" />
    </td>
</tr>
</table>
</form>
    </td>
</tr>
<tr>
    <td colspan="2">
<form method="get" action="index.php" name="sloc_form">
<input type="hidden" name="target" value="{$current_target}" />
<input type="hidden" name="field_filename" value="{$smarty.get.field_filename|escape}" />
<input type="hidden" name="field_path" value="{$smarty.get.field_path|escape}" />
<b>Storage location:h</b> 
{if count($storage_locations) eq 1}
<input type="hidden" name="storage_location_code" value="{$storage_location_code}" />
{foreach from=$storage_locations item=s_loc}{if $storage_location_code eq $s_loc.code}{$s_loc.title}{/if}{/foreach}
{else}
<select name="storage_location_code" onchange="javascript: cw_submit_form('sloc_form');">
{foreach from=$storage_locations item=s_loc}
<option value="{$s_loc.code}" {if $storage_location_code eq $s_loc.code}selected="selected"{/if}>{$s_loc.title}</option>
{/foreach}
</select>
{/if}
&nbsp;&nbsp;<b>Current folder:</b> {$smarty.get.dir|escape|default:'storage root'}
</form>
<br>
    </td>
</tr>
<tr>
	<td width="50%" valign="top" style="padding: 0 5px;">

<form method="get" onsubmit="javascript: return checkDirectory();" name="dir_form">
<input type="hidden" name="target" value="{$current_target}" />
<input type="hidden" name="storage_location_code" value="{$storage_location_code}" />
<input type="hidden" name="field_filename" value="{$smarty.get.field_filename|escape}" />
<input type="hidden" name="field_path" value="{$smarty.get.field_path|escape}" />
{if $product_warehouse}
<input type="hidden" name="product_warehouse" value="{$product_warehouse|escape}" />
{/if}

<b>{$lng.lbl_directories}:</b><br />
<select name="dir" class="auto_height" size="10" style="width: 100%" ondblclick="javascript: if (checkDirectory()) document.dir_form.submit();">
{section name=idx loop=$dir_entries}
{if $dir_entries[idx].filetype eq "dir"}
	<option value="{$dir_entries[idx].href}">{$dir_entries[idx].file|truncate:35}/</option>
{/if}
{/section}
</select>
<div class="clear"></div>
<input type="submit" value="{$lng.lbl_change_directory|strip_tags:false|escape}" />
</form>

	</td>
	<td width="50%" valign="top"  style="padding: 0 5px;">

<form method="post" name="files_form">
<input type="hidden" name="target" value="{$current_target}" />
<input type="hidden" name="storage_location_code" value="{$storage_location_code}" />
<input type="hidden" name="action" value="" />
<input type="hidden" name="dir" value="{$smarty.get.dir|escape:"html"}" />
<input type="hidden" name="field_filename" value="{$smarty.get.field_filename|escape:"html"}" />
<input type="hidden" name="field_path" value="{$smarty.get.field_path|escape:"html"}" />
<b>{$lng.lbl_files}:</b>
<div class="clear"></div>
<select name="path" class="auto_height" size="10" style="width: 100%" ondblclick="javascript: setFileInfo();">
{section name=idx loop=$dir_entries}
{if $dir_entries[idx].filetype ne "dir"}
	<option value="{$dir_entries[idx].href}">{$dir_entries[idx].file|truncate:35}</option>
{/if}
{/section}
</select>

<div class="clear"></div>

<input type="button" value="{$lng.lbl_select|strip_tags:false|escape}" onclick="javascript: setFileInfo();" />
&nbsp;
<input type="button" value="{$lng.lbl_delete|strip_tags:false|escape}" onclick="javascript: if (document.files_form.path.value != '') cw_submit_form('files_form', 'delete_file'); else alert(err_choose_file_first);" />

</form>

	</td>
</table>
{/capture}

<div align="center">
{include file="common/section.tpl" content=$smarty.capture.section title=$lng.lbl_choose_file style="files_section"}
</div>
