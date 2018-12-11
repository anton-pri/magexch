<div class="box image_popup">
{if $included_tab eq 'on_server'}
{* start *}
<table class="input_table">
<tr>
	<td id="on_server_box_0">
	<input type="hidden" name="file_paths[0]" id="file_paths_0" />
	<input type="text" size="25" name="filenames[0]" id="filename_0" readonly="readonly" />
	<input type="button" value="{$lng.lbl_browse_|strip_tags:false|escape}" onclick="javascript: popup_images('filename_0', 'file_paths_0');" />
	</td>
{if $multiple eq 2}
    <td id="on_server_add_button">{include file="main/multirow_add.tpl" mark="on_server" is_lined=true}</td>
{/if}
</tr>
</table>
{elseif $included_tab eq 'on_local'}
{* start *}
    <div id="file_err"></div>
<div class="warning">{$lng.txt_max_file_size_warning|substitute:"size":$upload_max_filesize}</div>
<table class="images_table">
<tr>
	<td id="on_local_box_0">
        <div {*class="btn fileinput-button"*}>
{*            <span>{$lng.lbl_add_image}</span> *}
            <input id="fileupload" type="file" size="25" name="userfiles[0]" accept="image/*" />
        </div><div id="progress"><div class="bar" style="width: 0%;"> <div class="fileupload-name"></div></div></div>




    </td>
{if $multiple eq 2}
    <td id="on_local_add_button">{include file="main/multirow_add.tpl" mark="on_local" is_lined=true}</td>
{/if}
</tr>
</table>

{elseif $included_tab eq 'on_internet'}
{* start *}
<table class="input_table">
<tr>
    <td id="on_internet_box_0">{$lng.lbl_url}:</td>
	<td id="on_internet_box_1"><input type="text" size="60" name="fileurls[0]" /></td>
{if $multiple eq 2}
    <td id="on_internet_add_button">{include file="main/multirow_add.tpl" mark="on_internet" is_lined=true}</td>
{/if}
</tr>
</table>
{/if}
</div>
