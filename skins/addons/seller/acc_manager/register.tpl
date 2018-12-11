{if $config.Security.use_https_login eq "Y"}
{assign var="form_url" value=$catalogs_secure.$app_area}
{else}
{assign var="form_url" value=$catalogs.$app_area}
{/if}

<script type="text/javascript">
{literal}
$(document).ready(function(){
        $('form[name="register_seller_form"]').validate();
});
{/literal}
</script>

<form action="{$form_url}/index.php?target=acc_manager" method="post" name="register_seller_form" id="register_seller_form">
    <input type="hidden" name="action" value="register_seller" />

    <div class="input_field_1">
        <label class='required'>{$lng.lbl_email}</label>
        <input type="text" name="register[email]" size="30" value="{$prefilled.email|escape}" class='required' />
    </div>

    <div class="input_field_1">
        <label class='required'>{$lng.lbl_password}</label>
        <input type="password" name="register[password]" id='password' size="30" maxlength="64" value="{$prefilled.password}" class='required' />
    </div>

    <div class="input_field_1">
        <label class='required'>{$lng.lbl_password}</label>
        <input type="password" name="register[password2]" equalTo='#password' size="30" maxlength="64" value="{$prefilled.password2}" class='required' />
    </div>
    
    {if $addons.image_verification and $show_antibot.on_registration eq 'Y'}
    {include file='addons/image_verification/spambot_arrest.tpl' mode='simple' id=$antibot_sections.on_registration}
    {/if}

    {include file='buttons/submit.tpl' href="javascript:cw_submit_form('register_seller_form')" style='btn'}

    <input type='submit' style='display:none;' hidefocus="true" tabindex="-1" />

</form>
