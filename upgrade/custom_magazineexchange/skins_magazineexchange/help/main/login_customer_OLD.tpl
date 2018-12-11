<div class="gray_border">
<div class="login_block">
{if $config.Security.use_https_login eq "Y"}
{assign var="form_url" value=$catalogs_secure.$app_area}
{else}
{assign var="form_url" value=$catalogs.$app_area}
{/if}
<div class="help_box">
  <h2>{$lng.lbl_log_into_cust_acc}</h2>
<div class="box_content" style="padding-top:27px;">
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

<!--
<div class="auth_buttons login_button">{include file='buttons/login_menu.tpl'}
</div>
-->
<div class="auth_buttons login_button">
    <button onclick="javascript: cw_submit_form('auth_form');" class="btn btn-green btn-auth">{$lng.lbl_log_in}</button></div>


<!--
<div class="login_note">{$lng.lbl_problems_logging} <a href="">{$lng.lbl_get_help}</a></div>
-->

{if !$customer_id}
{*include file='buttons/social_media_panel.tpl'*}
{/if}
</form>
<div class="clear"></div>
</div>

</div>
<div style="text-align:center;width:335px; height:25px; font-size:12pt;"> - Or - </div>

<div class="help_box" style="width: 335px; background: #f2f2f2;">
  <h2>{$lng.lbl_dont_have_acc}</h2>
    <div class="box_content" style="background: #f2f2f2;">{$lng.txt_login_page}
        {if $usertype eq "C" or ($usertype eq "B" and $config.Salesman.salesman_register eq "Y")}

       <!--   <div class="register_button">{include file='buttons/create_profile_menu.tpl'}</div>  -->


<div class="register_button">
    <button onclick="location.href='index.php?target=acc_manager&usertype=C';" class="btn btn-minw btn-default btn-danger">{$lng.lbl_register}</button></div>



        {/if}

    </div>
</div> 
<div>
	<a href="{$catalogs.customer}/seller/index.php"><img style="display: block;
	left: 420px;
	position: absolute;
	top: 95px;
	width: 514px;" src="{$AltImagesDir}/Customer_Login_Avatar.png" alt="Click Here to Login to your SELLER account " width="514" height="388" class="hide_tablet"></a>
</div>
</div>
</div>