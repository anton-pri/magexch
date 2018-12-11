{if $config.Security.use_https_login eq "Y"}
{assign var="form_url" value=$catalogs_secure.$app_area}
{else}
{assign var="form_url" value=$catalogs.$app_area}
{/if}
<form action="{$form_url}/index.php?target={if $current_area eq 'C'}acc_manager{else}login{/if}" method="post" name="need_login" style='float:left'>
<input type="hidden" name="{$APP_SESS_NAME}" value="{$APP_SESS_ID}" />
<input type="hidden" name="action" value="login" />
<div class="input_field_easy_0">
    <label>{$lng.lbl_email}</label><br/>
    <input type="email" name="email" size="25" value="{#default_login#|default:$email}" />
</div>
<div class="input_field_easy_0">
    <label>{$lng.lbl_password}</label><br/>
    <input type="password" name="password" size="25" maxlength="64" value="{#default_password#}" onkeypress="javascript: return submitEnter(event);"/><br />
</div>
<div class="auth_buttons">
<div class="button_left_align">
{include file='buttons/button.tpl' button_title=$lng.lbl_log_in  href="javascript: void(0);" onclick="submitFormAjax('need_login');" image_menu=true  style='small'}
</div>
{if $usertype eq "C" or ($usertype eq "B" and $config.Salesman.salesman_register eq "Y")}
{include file='buttons/create_profile_menu.tpl'}
{/if}
</div>
{if !$customer_id}
  {capture assign="social_media_panel_content"}{include file='buttons/social_media_panel.tpl'}{/capture}
  {if $social_media_panel_content ne ''}
    <div class="social_login">
      <a href="javascript:void(0);" onclick="social_media_closePopup()">{$lng.lbl_social_media_login}</a>

      <div id="social_block" style="display: none;">
        {include file='buttons/social_media_panel.tpl'}
      </div>

    </div>
  {/if}
<div class="password_recovery"><a href="{pages_url var='help' section='password'}">{$lng.lbl_recover_password}</a></div>
{/if}
</form>
<div style='float:right; width: 50%;'  class="login_comment">{$lng.txt_login_page}</div>
