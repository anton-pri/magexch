{capture name=section}

{include file='addons/seller/acc_manager/register.tpl'}

{/capture}
{include file='common/section.tpl' title=$lng.lbl_register_seller content=$smarty.capture.section style='simple'}
