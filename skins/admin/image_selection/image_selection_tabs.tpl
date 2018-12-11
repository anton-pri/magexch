<div class="box image_popup">
{if $included_tab eq 'on_server'}
{* start *}
<div class="form-group row">
	<div id="on_server_box_0"> 
	<input type="hidden" name="file_paths[0]" id="file_paths_0" />
	<div class="col-xs-8"><input type="text" size="25" name="filenames[0]" id="filename_0" readonly="readonly" class="form-control"/></div>
	<div class="col-xs-4"><input type="button" class="form-control" value="{$lng.lbl_browse_|strip_tags:false|escape}" onclick="javascript: popup_images('filename_0', 'file_paths_0');" /></div>
	</div>
{if $multiple eq 2}
    <div id="on_server_add_button">{include file="main/multirow_add.tpl" mark="on_server" is_lined=true}</div>
{/if}
</div>
{elseif $included_tab eq 'on_local'}
{* start *}
    <div id="file_err"></div>
<div class="warning">{$lng.txt_max_file_size_warning|substitute:"size":$upload_max_filesize}</div>
<div class="form-group">
	<div id="on_local_box_0">
        <div {*class="btn fileinput-button"*}>
{*            <span>{$lng.lbl_add_image}</span> *}
            <input id="fileupload" type="file" size="25" name="userfiles[0]" accept="image/*"  class="input-file" />
        </div><div id="progress"><div class="bar" style="width: 0%;"> <div class="fileupload-name"></div></div></div>




    </div>
{if $multiple eq 2}
    <div id="on_local_add_button">{include file="main/multirow_add.tpl" mark="on_local" is_lined=true}</div>
{/if}
</div>

{elseif $included_tab eq 'on_internet'}
{* start *}
<div class="form-group row">
    <div id="on_internet_box_0" class="col-xs-12">{$lng.lbl_url}:</div>
	<div id="on_internet_box_1" class="col-xs-12"><input type="text" class="form-control" size="60" name="fileurls[0]" /></div>
{if $multiple eq 2}
    <div id="on_internet_add_button">{include file="main/multirow_add.tpl" mark="on_internet" is_lined=true}</div>
{/if}
</div>
{/if}
</div>
