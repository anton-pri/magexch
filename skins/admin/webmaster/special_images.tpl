{include file='main/select/edit_lng.tpl' script="index.php?target=`$current_target`"}
{capture name=section}
<div class="form-horizontal">
<form action="index.php?target={$current_target}" method="post" name="in_images">
<input type="hidden" name="action" value="update" />

{foreach from=$in_images key=name item=image}
{capture name=block}

{*include file='common/subheader.tpl' title=$image.title*}
<a name="{$name}"></a>

<div class="form-group special_image">
	<div class="col-xs-12">{include file='admin/images/edit.tpl' idtag="edit_image_`$image.id`" delete_js="cw_submit_form('in_images', 'delete_`$image.id`');"}</div>
</div>

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_image_title}:</label>
    <div class="col-xs-12">
    	<input type="text" class="form-control" name="image_data[{$image.id}][image_title]" value="{$image.image_title}">
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_image_link}:</label>
    <div class="col-xs-12">
    	<input type="text" class="form-control" name="image_data[{$image.id}][link]" value="{$image.link}">
    </div>
</div>
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_alternative_text}:</label>
    <div class="col-xs-12">
    	<input type="text" class="form-control" name="image_data[{$image.id}][alt]" value="{$image.alt}">
    </div>
</div>

{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block extra='width="100%"' title=$image.title}

{/foreach}

<div class="buttons"><input type="submit" value="{$lng.lbl_save}" class="btn btn-green push-20"></div>
</form>
</div>
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_special_images}
