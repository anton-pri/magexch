{*include file='common/page_title.tpl' title=$lng.lbl_modify_payment_method*}
{capture name=section}

{include file='main/select/edit_lng.tpl' script="index.php?target=payments&mode=methods&payment_id=`$payment_id`"}

{jstabs}
default_tab={$js_tab|default:"info"}

{if $accl.__2503}
[submit]
title="{$lng.lbl_save}"
style="btn-green push-20 push-l-20"
href="javascript: cw_submit_form('payment_method_form');"
{/if}

[info]
title={$lng.lbl_payment_info}
template="addons/payment_system/admin/method_info.tpl"

{if !$payment.is_quotes}
[web]
title={$lng.lbl_payment_web_info}
template="addons/payment_system/admin/method_web.tpl"
{/if}

{if !$payment.is_web}
[quotes]
title={$lng.lbl_payment_quotes}
template="addons/payment_system/admin/method_quotes.tpl"
{/if}

{/jstabs}

<form action="index.php?target=payments" method="post" name="payment_method_form">
<input type="hidden" name="mode" value="{$mode}" />
<input type="hidden" name="js_tab" id="form_js_tab" value="" />
<input type="hidden" name="payment_id" value="{$payment_id}" />
<input type="hidden" name="action" value="update_method" />
<div class="block">
  {include file='admin/tabs/js_tabs.tpl' name='payment_info'}
</div>

</form>
{/capture}
{include file="admin/wrappers/section.tpl" content=$smarty.capture.section extra='width="100%"' title=$lng.lbl_modify_payment_method}

