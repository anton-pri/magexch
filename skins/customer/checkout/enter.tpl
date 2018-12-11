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
    <div class="password-recovery"><a href="{pages_url var='help' section='password'}">{$lng.lbl_forgot_your_password}</a></div>
    {/if}

    {include file="buttons/login_checkout.tpl"}
    <div class='clear'></div>

</form>

{/if}
