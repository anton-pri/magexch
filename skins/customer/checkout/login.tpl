<script type="text/javascript">
$(document).ready(cw_register_init);
customer_id = '{$customer_id}';
</script>

{if !$customer_id}
{*<h2>2) {$lng.lbl_sign_in}</h2>*}

<form action="{$app_web_dir}/index.php?target=acc_manager" method="post" name="osc_auth_form">
    <input type="hidden" name="action" value="login_customer" />
    <input type="hidden" name="is_checkout" value="1" />
    <input type='submit' style='display:none;' hidefocus="true" tabindex="-1" />
    <div class="input_field_easy_0">
        <label>{$lng.lbl_email}</label>
        <input type="email" name="email" size="16" value="{#default_login#|default:$email}" />
    </div>
    <div class="input_field_easy_0">
        <label>{$lng.lbl_password}</label>
        <input type="password" name="password" size="16" maxlength="64" value="{#default_password#}" />
    </div>
    {include file="buttons/login_checkout.tpl"}
    <div class='clear'></div>

    {if !$customer_id}
    <div class="password-recovery">{$lng.lbl_osc_have_account} <a href="{pages_url var='help' section='password'}">{$lng.lbl_recover_password}</a></div>
    {/if}
</form>

{/if}


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

<!-- block Account -->
<div class="checkout_block" style="min-height: 10px;">
<h2>{if $customer_id}{$lng.lbl_your_account}{else}<span id="cr-acc-header">{$lng.lbl_create_your_account}</span>{/if}</h2>

{if $customer_id}
  <div class="logged_user">
    <form action="{$app_web_dir}/index.php?target=acc_manager&action=logout" method="post" name="osc_logout_form">
    {$user_account.email} {$lng.txt_logged_in}<br/>
    {*include file="buttons/button.tpl" button_title=$lng.lbl_logoff href="javascript: cw_submit_form('osc_logout_form');"*}
    </form>
  </div>
{/if}
</div>
<!-- /block Account  -->

<!-- block Register -->
<div class="checkout_block">

<form action="index.php?target=acc_manager&action=update" method="post" name="register_form" id='profile_form'>
{if !$customer_id}
    <input type='hidden' name='mode' value='add' />
    <div class="width50">
      {include file='customer/acc_manager/modify.tpl' included_tab='basic' current_target='acc_manager' is_checkout=1}
    </div>
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
{include file="buttons/button.tpl" button_title=$lng.lbl_apply onclick="javascript: cw_checkout_save_addresses()" style="button"}
</div>


</form>
</div>
<!-- block Register -->

</div> <!-- class=grey_opc -->
</div> <!-- id=osc-login-new -->
