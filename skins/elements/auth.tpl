{capture name=menu}
{if $config.Security.use_https_login eq "Y"}
{assign var="form_url" value=$catalogs_secure.$app_area}
{else}
{assign var="form_url" value=$catalogs.$app_area}
{/if}
<form action="{$form_url}/index.php?target={if $current_area eq 'C'}acc_manager{else}login{/if}" method="post" name="auth_form">
<input type="hidden" name="action" value="login" />
<div class="input_field_easy_0">
    <label>{$lng.lbl_email}</label><br/>
    <input type="email" name="email" size="18" value="{#default_login#|default:$email}" />
</div>
<div class="input_field_easy_0">
    <label>{$lng.lbl_password}</label><br/>
    <input type="password" name="password" size="18" maxlength="64" value="{#default_password#}" onkeypress="javascript: return submitEnter(event);"/><br />
</div>
<div class="auth_buttons">{include file='buttons/login_menu.tpl'}
{if $usertype eq "C" or ($usertype eq "B" and $config.Salesman.salesman_register eq "Y")}
{include file='buttons/create_profile_menu.tpl'}
{/if}
</div>
<!-- cw@recover_pass [ -->
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
<div class="password_recovery"><a href="{pages_url var='help' section='password'}">{$lng.lbl_recover_password}</a></div>
{/if}
<!-- cw@recover_pass ] -->

</form>
{/capture}
{include file='common/menu.tpl' title=$lng.lbl_authentication content=$smarty.capture.menu}

