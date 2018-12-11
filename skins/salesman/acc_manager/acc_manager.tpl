{capture name=section}

{include file='salesman/acc_manager/register_salesman.tpl'}

{include file='salesman/acc_manager/login_salesman.tpl'}

<div align="right">
    {capture name="page_url"}{pages_url var='help' section='password'}{/capture}
    {include file='buttons/button.tpl' button_title=$lng.lbl_recover_password href=$smarty.capture.page_url style='top'}
</div>

{/capture}
{include file='common/section.tpl' title=$lng.lbl_login_salesman content=$smarty.capture.section style='simple'}
