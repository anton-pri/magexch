<div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
{capture name=menu}
{if $config.Security.use_https_login eq "Y"}
{assign var="form_url" value=$catalogs_secure.$app_area}
{else}
{assign var="form_url" value=$catalogs.$app_area}
{/if}
<form action="{$form_url}/index.php?target=login" method="post" name="auth_form" class="push-50 push-30-t">
<input type="hidden" name="action" value="login" />
<div class="form-group">
      <label for="email">{$lng.lbl_email}:</label>
      <div id="err_empty_email" style="display:none; color: red;">Please enter valid email:</div>
      <input class="form-control" type="text" name="email" size="18" value="{#default_login#|default:$email}" />
</div>
<div class="form-group">
      <label>{$lng.lbl_password}:</label>
      <input class="form-control" type="password" name="password" size="18" maxlength="64" value="{#default_password#}" onkeypress="javascript: return submitEnter(event);"/><br />
</div>
<div class="form-group">
    <button onclick="javascript: cw_submit_form('auth_form');" class="btn btn-green btn-auth">{$lng.lbl_log_in}<i class="si si-login pull-right"></i></button>
</div>

{if $usertype eq "C" or ($usertype eq "B" and $config.Salesman.salesman_register eq "Y")}
{include file='buttons/create_profile_menu.tpl'}<br/>
{/if}

{if !$customer_id}
<script type="text/javascript">
{literal}
function admin_login_validateEmail(email) {
    //var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
    var re = /\S+@\S+\.\S+/;
    return re.test(email);
}
function submit_password_recovery() {
    if (admin_login_validateEmail(document.auth_form.email.value)) {
       document.auth_form.action.value = 'password_recovery';
       cw_submit_form('auth_form');
    } else {
       document.getElementById('err_empty_email').style.display='';
       document.auth_form.email.focus();
    }
}
{/literal}
</script>

{/if}
</form>
{/capture}
{include file='admin/wrappers/menu.tpl' title=$lng.lbl_authentication content=$smarty.capture.menu style=green options=password_recovery}
</div>