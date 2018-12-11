<script type="text/javascript" language="JavaScript 1.2">
<!--
var err_choose_file_first = "{$lng.err_choose_file_first|escape:javascript|strip_tags|replace:"\n":" "|replace:"\r":" "}";
var err_choose_directory_first = "{$lng.err_choose_directory_first|escape:javascript|strip_tags|replace:"\n":" "|replace:"\r":" "}";
var field_filename = "{$field_filename}";
var field_path = "{$field_path}";

{literal}
function setFile(filename, path) {
	if (window.opener) {

		var badfilename_pattern=new RegExp("\.\.\.$");
	    if (badfilename_pattern.test(filename) != false) {
	    	filename = path.replace(/.*[\\/]/g, '');
	    }
		
		if (window.opener.document[field_filename])
			window.opener.document[field_filename].value = filename;
		else if (window.opener.document.getElementById(field_filename))
			window.opener.document.getElementById(field_filename).value = filename;

		if (window.opener.document[field_path])
			window.opener.document[field_path].value = path;
		else if (window.opener.document.getElementById(field_path))
			window.opener.document.getElementById(field_path).value = path;

	}
	window.close ();
}

function setFileInfo() {
	if (document.files_form.path.value != "") {
		setFile(document.files_form.path.options[document.files_form.path.selectedIndex].text, document.files_form.path.value);
	} else {
		alert(err_choose_file_first);
	}
}

function setFilePreview() {
	if (document.files_form.path.value != "") {
		document.files_form.file_preview.value = document.files_form.path.value;
		document.files_form.submit();
	} else {
		alert(err_choose_file_first);
	}
}


function checkDirectory() {
	if (document.dir_form.dir.selectedIndex == -1) {
		alert(err_choose_directory_first);
		return false;
	}

	return true;
}

function setImagePreview() {
	if (document.files_form.enable_preview.checked)
		document.preview.src = 'getfile.php?file='+document.files_form.path.value.replace(/&/, "%26");
}

{/literal}
-->
</script>
</head>
<body class="background"{$reading_direction_tag}>
<table cellpadding="10" cellspacing="0" width="100%"><tr><td>

{assign var="width" value="width=33%"}
<br />
{capture name=section}

<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td {*$width*} valign="top" colspan="2" style="padding: 10px;">
<b>&nbsp;</b>

<center>
{if $file_preview}
<img src="getfile.php?file={$file_preview}" name="preview" width="100" height="100" alt="{$lng.lbl_preview_image|escape}" /><br />
{else}
<img src="{$ImagesDir}/null.gif" name="preview" width="100" height="100" border="1" alt="{$lng.lbl_preview_image|escape}" /><br />
{/if}
<br />
<input type="checkbox" name="enable_preview" value="Y" checked="checked" /> Active
<table cellpadding="0" cellspacing="2" width="100%"><tr>
<td><div align="justify">{$lng.txt_preview_images_note}</div></td>
</tr></table>
</center>
</td>
</tr>


<tr>
<td {$width} valign="top" style="padding: 10px;">
<form method="get" onsubmit="javascript: return checkDirectory ()" name="dir_form" action="index.php">
<input type="hidden" name="target" value="{$current_target}" />
<input type="hidden" name="field_filename" value="{$smarty.get.field_filename}" />
<input type="hidden" name="field_path" value="{$smarty.get.field_path}" />
<input type="hidden" name="action" value="{$mode}" />
<input type="hidden" name="tp" value="images" />

<b>{$lng.lbl_directories}:</b><br />
<select name="dir" size="20" style="width: 100%" ondblclick="javascript: if(checkDirectory()) document.dir_form.submit();">
{section name=idx loop=$dir_entries}
{if $dir_entries[idx].filetype eq "dir"}
<option value="{$dir_entries[idx].href}">{$dir_entries[idx].file|truncate:35}/</option>
{/if}
{/section}
</select><br /><br />
<center>
<input type="submit" value="{$lng.lbl_change_directory|strip_tags:false|escape}" /></center></form>
</td>

<form method="get" name="files_form" action="index.php">
<input type="hidden" name="target" value="{$current_target}" />

<td {$width} valign="top" style="padding: 10px;">
<input type="hidden" name="dir" value="{$smarty.get.dir|escape:"html"}" />
<input type="hidden" name="field_filename" value="{$smarty.get.field_filename|escape:"html"}" />
<input type="hidden" name="field_path" value="{$smarty.get.field_path|escape:"html"}" />
<input type="hidden" name="action" value="{$mode}" />
<input type="hidden" name="action" value="" />
<input type="hidden" name="file_preview" value="" />
<input type="hidden" name="tp" value="images" />

<b>{$lng.lbl_files}:</b>
<select name="path" size="20" style="width: 100%" onchange="setImagePreview()" ondblclick="javascript: setFileInfo();">
{section name=idx loop=$dir_entries}
{if $dir_entries[idx].filetype ne "dir"}
<option value="{$dir_entries[idx].href}" {if $dir_entries[idx].href eq $file_preview}selected{/if}>{$dir_entries[idx].file|truncate:35}</option>
{/if}
{/section}
</select><br /><br />
<center>
<input type="button" value="{$lng.lbl_select|strip_tags:false|escape}" onclick="javascript: setFileInfo ();" />
</center>
</td>


</form>
</tr>
</table>
{/capture}
<div align="center">
{include file="common/section.tpl" content=$smarty.capture.section title=$lng.lbl_choose_file extra='width="100%"'}
</div>

<p align="right"><a href="javascript:window.close();"><b>{$lng.lbl_close_window}</b></a></p>
</td></tr></table>
