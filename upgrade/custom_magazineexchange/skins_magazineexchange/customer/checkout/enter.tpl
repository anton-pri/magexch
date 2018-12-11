<script type="text/javascript">
$(document).ready(cw_register_init);
customer_id = '{$customer_id}';
</script>

{if !$customer_id}
{*<h2>2) {$lng.lbl_sign_in}</h2>*}
<div class="already_registered">
<form action="{$app_web_dir}/index.php?target=acc_manager" method="post" name="osc_auth_form">
    <input type="hidden" name="action" value="login_customer" />
    <input type="hidden" name="is_checkout" value="1" />
    <input type='submit' style='display:none;' hidefocus="true" tabindex="-1" />

    <div class="input_field_easy_0">
        <label class="checkout_login">{$lng.lbl_email} <font class="Star">*</font></label>
        <input type="email" name="email" size="16" value="{#default_login#|default:$email}" />
    </div>
    <div class="input_field_easy_0">
        <label class="checkout_login">{$lng.lbl_password} <font class="Star">*</font></label>
        <input type="password" name="password" size="16" maxlength="64" value="{#default_password#}" />
    </div>
    <div class="password-recovery"><a href="{pages_url var='help' section='password'}">{$lng.lbl_forgotten_login_details}</a></div>

    {if !$customer_id}
  {capture assign="social_media_panel_content"}{include file='buttons/social_media_panel.tpl'}{/capture}
  {if $social_media_panel_content ne ''}
    <div class="social_login">
      <a id="social_link"  href="javascript:void(0);">{$lng.lbl_social_media_login}</a>

      <div id="social_block" style="display: none;">
        {include file='buttons/social_media_panel.tpl'}
      </div>

    </div>
  {/if}
    {/if}
    <div class="buttons_centered">
    {include file="buttons/button.tpl" button_title=$lng.lbl_log_in href="javascript:cw_submit_form_ajax('osc_auth_form', 'cw_one_step_checkout_login')" style='login-button button'}
    </div>
    <div class='clear'></div>

</form>
</div>
{/if}
