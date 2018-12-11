<form action='{$current_location}/admin/index.php?target=webmaster&mode=modify' method='POST' name='webmaster_modify_form' {if in_array($type, array('products_images_det','products_images_thumb','cms_images'))}enctype="multipart/form-data"{/if}>
<input type='hidden' name='type' value='{$type}' />
<input type='hidden' name='key' value='{$key}' />
<input type='hidden' name='xss' value='{$xss}' />
{if in_array($type, array('products_images_det','products_images_thumb','cms_images'))}
{include file='addons/webmaster/webmaster_modify_image_form.tpl'}
{else}
{include file='addons/webmaster/webmaster_modify_form.tpl'}
<p>
<input type='button' value='Update' onclick='submitFormAjax("webmaster_modify_form")' />
</p>
{/if}
</form>
