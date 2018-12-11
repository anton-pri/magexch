{include_once_src file='main/include_js.tpl' src='js/popup_product.js'}
{include_once_src file='main/include_js.tpl' src='addons/deal_of_day/js/admin.js'}
{include_once file='categories_ajax/include_js.tpl'}

{*include file='common/page_title.tpl' title=$lng.lbl_dod_modify_generator*}
{capture name=section}
{jstabs}
default_tab={$js_tab|default:"dod_generator_details"}

[dod_generator_details]
title={$lng.lbl_dod_generator_details}
template="addons/deal_of_day/admin/generator_details.tpl"

[dod_generator_bonuses]
title={$lng.lbl_dod_generator_bonuses}
template="addons/deal_of_day/admin/generator_bonuses.tpl"

{/jstabs}



<form action="index.php?target={$current_target}" method="post" name="generator_details" id="generator_details">
<input type="hidden" name="mode" value="details" />
<input type="hidden" name="action" value="modify" />
<input type="hidden" name="generator_id" value="{$generator_data.generator_id}" />
<input type="hidden" name="js_tab" value="{$js_tab|default:'dod_generator_details'}" />

{include file='tabs/js_tabs.tpl' group="dod_generator"}
<div class="buttons">{include file='buttons/button.tpl' href="javascript: for (instance in CKEDITOR.instances ) CKEDITOR.instances[instance].updateElement();cw_submit_form('generator_details');" button_title=$lng.lbl_dod_button_save acl=$page_acl style="btn"}</div>
</form>
{/capture}
{include file="common/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_dod_modify_generator}
