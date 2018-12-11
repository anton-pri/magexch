<div id="register_customer">
{capture name=section2}
<p>{$lng.lbl_reg_text}</p>

<p>{$lng.txt_fields_are_mandatory}</p>
{include file="customer/acc_manager/register_customer.tpl"}
{/capture}
{include file='common/section.tpl' is_dialog=1 title=$lng.lbl_create_profile content=$smarty.capture.section2}
</div>
