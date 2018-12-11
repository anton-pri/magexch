<!-- skins_magazineexchange/_autoload/_seller/addons/seller/sections/basic.tpl -->

{if $current_area eq 'A'  && $userinfo.customer_id}
<table class="header_bordered">
<tr>
    <th>{$lng.lbl_created}</th>
    <td>by {$userinfo.system_info.customer_created_by} at {$userinfo.system_info.creation_date|date_format:$config.Appearance.datetime_format}</td>
</tr>
<tr>
    <th>{$lng.lbl_last_modified}</th>
    <td>by {$userinfo.system_info.customer_modified_by} at {$userinfo.system_info.modification_date|date_format:$config.Appearance.datetime_format}</td>
</tr>
</table>
{/if}


{if $current_area eq 'A' && $userinfo.customer_id}
<div class="input_field_0">
    <label>{$lng.lbl_user_id}</label>
    <input type="text" size="32" value="{$userinfo.customer_id}" disabled readonly class='short' />
</div>
{/if}

{if $profile_fields.basic.email.is_avail}
<div class="input_field_1">
    <label class='required jaseller_address_label'>{$lng.lbl_primary_email}</label>
    <input type="email" class='required email' name="update_fields[basic][email]" maxlength="64" value="{$userinfo.email}" />
    {if $fill_error.basic.email}<font class="field_error">&lt;&lt;</font>{/if}
</div>
{/if}

{if $current_area eq 'C' && $userinfo.customer_id}
    <a href="index.php?target=change_password">{$lng.lbl_chpass}</a>
{else}
    <div class="input_field_{$profile_fields.basic.password.is_required}" id="pw-inp-field">
        <label class='jaseller_address_label{if !$userinfo.customer_id && $profile_fields.basic.password.is_required} required{/if}'>{$lng.lbl_password}</label>
        <input type="password" autocomplete='off' {if !$userinfo.customer_id && $profile_fields.basic.password.is_required}class='required'{/if} name="update_fields[basic][password]" id='password' maxlength="64" value="" />
        {if $fill_error.basic.password}<font class="field_error">&lt;&lt;</font>{/if}
        {if $current_area eq 'A'}
            <label class="tip">
            <input type="checkbox" name="update_fields[basic][change_password]" value="1"{if $userinfo.change_password} checked="checked"{/if} />
            {$lng.lbl_reg_chpass}
            </label>
        {/if}
    </div>
    <div class="input_field_{$profile_fields.basic.password.is_required}" id="pw-conf-field">
        <label class='jaseller_address_label{if !$userinfo.customer_id && $profile_fields.basic.password.is_required} required{/if}'>{$lng.lbl_confirm_password}</label>
        <input type="password" id="password-confirm" {if !$userinfo.customer_id && $profile_fields.basic.password.is_required}class='requred'{/if} equalTo='#password' name="update_fields[basic][password2]" maxlength="64" value="" />
        {if $fill_error.basic.password}<font class="field_error">&lt;&lt;</font>{/if}
    </div>
{/if}

<div class="input_field_1">
    <label class='jaseller_address_label{if $profile_fields.basic.membership_id.is_required} required{/if}'>{$lng.lbl_membership}</label>
    {include file='main/select/membership.tpl' name='update_fields[basic][membership_id]' value=$userinfo.membership_id is_please_select=1 disabled=true}
    {include file='addons/custom_magazineexchange_sellers/main/select/membership.tpl' value=$userinfo.membership_id}

    {if $fill_error.basic.membership_id}<span class="field_error">&lt;&lt;</span>{/if}
</div>


{include file='main/users/sections/custom.tpl'}
