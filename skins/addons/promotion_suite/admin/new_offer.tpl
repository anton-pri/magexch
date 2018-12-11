{include_once_src file='main/include_js.tpl' src='js/popup_product.js'}
{include_once_src file='main/include_js.tpl' src='addons/promotion_suite/js/admin.js'}
{include_once file='categories_ajax/include_js.tpl'}
{capture name=section}
<div class="block">
{jstabs}
default_tab={$js_tab|default:"ps_offer_bonuses"}

[ps_offer_bonuses]
title={$lng.lbl_ps_offer_bonuses}
template="addons/promotion_suite/admin/offer_bonuses.tpl"

[ps_offer_conditions]
title={$lng.lbl_ps_offer_conditions}
template="addons/promotion_suite/admin/offer_conditions.tpl"

[ps_offer_details]
title={$lng.lbl_ps_offer_details}
template="addons/promotion_suite/admin/offer_details.tpl"
{/jstabs}

<form action="index.php?target={$current_target}" method="post" name="offer_details" id='offer_details'>
<input type="hidden" name="mode" value="details" />
<input type="hidden" name="action" value="add" />
<input type="hidden" name="js_tab" value="{$js_tab|default:'ps_offer_details'}" />
<div class="block">
	{include file='admin/tabs/js_tabs.tpl' group="ps_offer"}
	{include file='admin/buttons/button.tpl' href="javascript: for (instance in CKEDITOR.instances ) CKEDITOR.instances[instance].updateElement();cw_submit_form('offer_details');" button_title=$lng.lbl_ps_button_save acl=$page_acl style="btn-green push-20"}
</div>

</form>
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_ps_new_offer}
