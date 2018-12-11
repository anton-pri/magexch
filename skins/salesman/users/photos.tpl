<form action="index.php?target={$current_target}" method="post" name="photos_form">
<input type="hidden" name="mode" value="{$mode}" />
<input type="hidden" name="action" value="delete_photos" />
{include file='main/users/sections/photos.tpl' mode='list'}
</form>
<br/>

{include file='common/subheader.tpl' title=$lng.lbl_add_new}
<form action="index.php?target={$current_target}" method="post" name="upload_form">
<input type="hidden" name="mode" value="{$mode}" />
<input type="hidden" name="action" value="customer_images" />
{include file='main/users/sections/photos.tpl' mode='add'}
</form>
