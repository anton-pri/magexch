<div class="osc-login osc-section" id='osc-login-new'>
<script type="text/javascript">
<!--
customer_id='{$customer_id}';
lbl_im_signed_in='<label>{*$lng.lbl_im_signed_in*}</label>';
{literal}
if (customer_id!=0) $('#onestep_option_container').html(lbl_im_signed_in);
{/literal}

-->
</script>


<div class="grey_opc">
<div class="checkout_block">
<!-- cw@checkout_register [ -->

<h2>{if $customer_id}{$lng.lbl_your_account}{else}<span id="cr-acc-header">{$lng.lbl_create_your_account}</span>{/if}</h2>

{if $customer_id}
  <div class="logged_user">
    <form action="{$app_web_dir}/index.php?target=acc_manager&action=logout" method="post" name="osc_logout_form">
    {$user_account.email} {$lng.txt_logged_in}<br/>
    {*include file="buttons/button.tpl" button_title=$lng.lbl_logoff href="javascript: cw_submit_form('osc_logout_form');"*}
    </form>
  </div>
{/if}
<!-- cw@checkout_register ] -->


<form action="index.php?target=acc_manager&action=update" method="post" name="register_form" id='profile_form'>
    <input type='hidden' name='is_checkout' value='1' />
{if !$customer_id}
    <!-- cw@checkout_reg_form [ -->

    <input type='hidden' name='mode' value='add' />
    {include file='customer/acc_manager/modify.tpl' included_tab='basic' current_target='acc_manager' is_checkout=1}
    <!-- cw@checkout_reg_form ] -->

{/if}

{include file='customer/acc_manager/modify.tpl' included_tab='address' current_target='acc_manager' is_checkout=1}

{*foreach from=$profile_sections key=section_name item=section}
{if $section.is_avail}
    {include file='common/subheader.tpl' title=$section.section_title}
    {if !$section.is_default}
        {include file='main/users/sections/custom.tpl' included_tab=$section_name current_target='acc_maanger' is_checkout=1}
    {else}
        {include file='customer/acc_manager/modify.tpl' included_tab=$section_name current_target='acc_manager' is_checkout=1}
    {/if}
{/if}
{/foreach*}

<div id="apply_address" style='display: none'>
{include file="buttons/button.tpl" button_title=$lng.lbl_continue onclick="javascript: cw_checkout_save_addresses()" style="button"}
</div>

</form>
</div>
</div>

</div>
