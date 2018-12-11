<div class="gray_border">
<div class="login_block">
{if $config.Security.use_https_login eq "Y"}
{assign var="form_url" value=$catalogs_secure.$app_area}
{else}
{assign var="form_url" value=$catalogs.$app_area}
{/if}
<div class="help_box">
  <div class="box_title">{$lng.lbl_log_into_cust_acc}</div>
<div class="box_content">
<form action="{$form_url}/index.php?target={if $current_area eq 'C'}acc_manager{else}login{/if}" method="post" name="auth_form" style='float:left'>
<input type="hidden" name="action" value="login" />
<div class="input_field_easy_0">
    <label>{$lng.lbl_email} <span class="Star">*</span></label>
    <input type="email" name="email" size="25" value="{#default_login#|default:$email}" />
</div>
<div class="input_field_easy_0">
    <label>{$lng.lbl_password}<span class="Star">*</span></label>
    <input type="password" name="password" size="25" maxlength="64" value="{#default_password#}" onkeypress="javascript: return submitEnter(event);"/><br />
</div>
<div class="password_recovery"><a href="{pages_url var='help' section='password'}">{$lng.lbl_forgotten_login_details}</a></div>

<div class="auth_buttons login_button">{include file='buttons/login_menu.tpl'}
</div>
<div class="login_note">{$lng.lbl_problems_logging} <a href="">{$lng.lbl_get_help}</a></div>

{if !$customer_id}
{*include file='buttons/social_media_panel.tpl'*}
{/if}
</form>
<div class="clear"></div>
</div>

</div>
<div class="help_box">
    <div class="box_title">{$lng.lbl_dont_have_acc}</div>
    <div class="box_content" style="background: #f2f2f2;">{$lng.txt_login_page}
        {if $usertype eq "C" or ($usertype eq "B" and $config.Salesman.salesman_register eq "Y")}
          <div class="register_button">{include file='buttons/create_profile_menu.tpl'}</div>
        {/if}
        <div class="login_note">{$lng.learn_more_about_benefits}</div>

    </div>
</div> 
<!--<div class="login_seller_img">
	<a href="admin/home.php"><img src="{$AltImagesDir}/Customer_Login_Avatar.png" alt="Click Here to Login to your SELLER account " width="514" height="388" class="hide_tablet seller_login_img"></a>
</div>-->
</div>
</div>