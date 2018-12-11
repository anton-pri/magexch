{capture name='block'}

{if $manufacturer.manufacturer_id}
{include file='main/select/edit_lng.tpl' script="index.php?target=manufacturers&manufacturer_id=`$manufacturer.manufacturer_id`&"}
{/if}
<form action="index.php?target=manufacturers" method="post" enctype="multipart/form-data" name="manufacturer_form" id='manufacturer_form' class="form-horizontal">
<input type="hidden" name="action" value="details" />
<input type="hidden" name="manufacturer_id" value="{$manufacturer.manufacturer_id}" />
<input type="hidden" name="page" value="{$page}" />
<div class="box">
<div class="form-group">
	<label class='multilan required col-xs-12'>{$lng.lbl_manufacturer}</label>
	<div class="col-xs-12">
		<input type="text" class='required form-control' name="manufacturer_update[manufacturer]" size="50" value="{$manufacturer.manufacturer}" />
	</div>
</div>

<div class="form-group">
	<label class="col-xs-12">{$lng.lbl_logo}</label>
	<div class="col-xs-12">
		{include file='admin/images/edit.tpl' image=$manufacturer.image delete_url="index.php?target=manufacturers&action=delete_image&manufacturer_id=`$manufacturer.manufacturer_id`" in_type='manufacturer_images'}
	</div>
</div>

<div class="form-group">
	<label class='multilan col-xs-12'>{$lng.lbl_description} </label>
    <div class="col-xs-12">
    	{include file='admin/textarea.tpl' name="manufacturer_update[descr]" cols=55 rows=10 data=$manufacturer.descr}
    </div>
</div>

<div class="form-group">
	<label for="url" class="col-xs-12">{$lng.lbl_url}</label>
	<div class="col-xs-12">
		<input type="text" size="50" class="form-control" name="manufacturer_update[url]" id="url" value="{$manufacturer.url}" />
	</div>
</div>

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_featured}: <input type="checkbox" name="manufacturer_update[featured]" value="1"{if $manufacturer.featured || $manufacturer.manufacturer_id eq ''} checked="checked"{/if} />&nbsp;</label>
</div>

<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_active}: <input type="checkbox" name="manufacturer_update[avail]" value="1"{if $manufacturer.avail || $manufacturer.manufacturer_id eq ''} checked="checked"{/if} /></label>
</div>

{if $addons.estore}
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_man_show_logo}: <input type="checkbox" name="manufacturer_update[show_image]" value="1"{if $manufacturer.show_image} checked="checked"{/if} /></label>
</div>
{/if}

{include file='admin/attributes/object_modify.tpl'}

</div>
<div id="sticky_content" class="buttons">
{include file='admin/buttons/button.tpl' button_title=$lng.lbl_save href="javascript:cw_submit_form('manufacturer_form');" acl='__1201' style='btn-green push-20'}
</div>

</form>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block extra='width="100%"' title=$lng.lbl_manufacturers section_id='manufacturer_info'}

