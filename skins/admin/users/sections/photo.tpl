
<input type="hidden" name="user_image_id" value="{$userphoto.image_id}" />
{if $current_area eq "C"}
<input type="hidden" name="mode" value="" />
<input type="hidden" name="user" value="{$user}" />
{/if}

{include file='admin/images/edit.tpl' image=$userphoto button_name=$lng.lbl_select in_type='customers_images' delete_js="document.forms.profile_form.action.value='delete_photos'; document.forms.profile_form.mode.value='photos'; cw_submit_form('profile_form', 'delete_photos');"}

<div class="buttons">
{if $current_area eq "C"}
    {include file='admin/buttons/button.tpl' button_title=$lng.lbl_save href="javascript: void(0);" onclick="document.forms.profile_form.action.value='customer_images'; document.forms.profile_form.mode.value='photos'; cw_submit_form('profile_form', 'customer_images');" style='btn-green push-20 push-20-t'}
{else}
    {include file='admin/buttons/button.tpl' button_title=$lng.lbl_save href="javascript: void(0);" onclick="document.forms.profile_form.action.value='customer_images'; document.forms.profile_form.mode.value='photos'; cw_submit_form('profile_form', 'customer_images');" style='btn-green push-20 push-20-t'}
{/if}
</div>
