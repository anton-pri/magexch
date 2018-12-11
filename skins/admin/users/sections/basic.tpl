{if $current_area eq 'A'  && $userinfo.customer_id}
<table class="table table-striped dataTable vertical-center">
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

<div class="form-horizontal">
{if $current_area eq 'A' && $userinfo.customer_id}
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_user_id}</label>
    <div class="col-xs-12">{$userinfo.customer_id}</div>
</div>
{/if}

{if $profile_fields.basic.email.is_avail}
<div class="form-group">
    <label class='required col-xs-12'>{$lng.lbl_primary_email}</label>
    <div class="col-xs-12">
    	<input type="email" class='required email form-control' name="update_fields[basic][email]" maxlength="128" value="{$userinfo.email}" />
    	{if $fill_error.basic.email}<font class="field_error">&lt;&lt;</font>{/if}
    </div>
</div>
{/if}

{if $current_area eq 'C' && $userinfo.customer_id}
    <a href="index.php?target=change_password" class="change_pass">{$lng.lbl_chpass}</a>
{else}
    <div class="form-group input_field_{$profile_fields.basic.password.is_required}" id="pw-inp-field">
        <label class='col-xs-12 {if !$userinfo.customer_id && $profile_fields.basic.password.is_required}required{/if}'>{$lng.lbl_password}</label>
		<div class="col-xs-12">
        	<input type="password" class="form-control" autocomplete='off' {if !$userinfo.customer_id && $profile_fields.basic.password.is_required}class='required'{/if} name="update_fields[basic][password]" id='password' maxlength="64" value="" />
        	{if $fill_error.basic.password}<font class="field_error">&lt;&lt;</font>{/if}
        	{if $current_area eq 'A'}
            	<label class="tip">
                <input type="hidden" name="update_fields[basic][change_password]" value="0" />
            	<input type="checkbox" name="update_fields[basic][change_password]" value="1"{if $userinfo.change_password} checked="checked"{/if} />
            	{$lng.lbl_reg_chpass}
            	</label>
        	{/if}
        </div>
    </div>
    <div class="form-group input_field_{$profile_fields.basic.password.is_required}" id="pw-conf-field">
        <label class='col-xs-12 {if !$userinfo.customer_id && $profile_fields.basic.password.is_required}required{/if}'>{$lng.lbl_confirm_password}</label>
    	<div class="col-xs-12">
        	<input type="password" class="form-control" id="password-confirm" {if !$userinfo.customer_id && $profile_fields.basic.password.is_required}class='requred'{/if} equalTo='#password' name="update_fields[basic][password2]" maxlength="64" value="" />
        	{if $fill_error.basic.password}<font class="field_error">&lt;&lt;</font>{/if}
        </div>
    </div>
{/if}

{if $profile_fields.basic.membership_id.is_avail}
<div class="form-group input_field_{$profile_fields.basic.membership_id.is_required}">
    <label class='col-xs-12 {if $profile_fields.basic.membership_id.is_required}required{/if}'>{$lng.lbl_membership}</label>
    <div class="col-xs-12">
    	{include file='admin/select/membership.tpl' name='update_fields[basic][membership_id]' value=$userinfo.membership_id is_please_select=1}
    	{if $fill_error.basic.membership_id}<span class="field_error">&lt;&lt;</span>{/if}
    </div>
</div>
{/if}

{if $profile_fields.basic.status.is_avail && $current_area eq 'A'}
<div class="form-group">
    <label class="col-xs-12">{$lng.lbl_status}</label>
    <div class="col-xs-12">
    	{include file='admin/select_user_status.tpl' name="update_fields[basic][status]" status_note=update_fields[basic][status_note] usertype=$userinfo.usertype value=$userinfo.status value_note=$userinfo.additional_info.status_note}
	</div>
</div>
{/if}

{* Non used fields
==========================================================================
{include_once_src file="main/include_js.tpl" src="js/elm_visibility.js"}

{if $userinfo.usertype ne 'I' && $userinfo.usertype ne 'A'}
{include file='main/users/fields/ssn.tpl'}
{/if}

{if $profile_fields.basic.tax_number.is_avail}
<div class="input_field_{$profile_fields.basic.tax_number.is_required}">
    <label class="col-xs-12">{if $userinfo.usertype eq 'R'}{$lng.lbl_tax_number_reseller}{else}{$lng.lbl_tax_number}{/if}</label>
	<div class="col-xs-12">
    	<input type="text" name="update_fields[basic][tax_number]" maxlength="32" value="{$userinfo.additional_info.tax_number}" />
    	{if $fill_error.basic.tax_number}<font class="field_error">&lt;&lt;</font>{/if}
    </div>
</div>
{/if}

{if $profile_fields.basic.chamber_certificate.is_avail}
{if $userinfo.chamber_certificate_file.file_id}
<div class="form-group">
    <label>{$lng.lbl_chamber_certificate}</label>
    <a href="{$userinfo.chamber_certificate_file.file_url}">{$lng.lbl_download}</a>
</div>
{/if}
<div class="input_field_{$profile_fields.basic.chamber_certificate.is_required}">
    <label>{$lng.lbl_chamber_certificate}</label>
    <input type="file" id="chamber_certificate" name="chamber_certificate" maxlength="32" value="{$userinfo.additional_info.chamber_certificate}" />
    {if $fill_error.basic.chamber_certificate}<font class="field_error">&lt;&lt;</font>{/if}
</div>
{/if}


{if $profile_fields.basic.language.is_avail}
<div class="input_field_{$profile_fields.basic.language.is_required}">
    <label>{$lng.lbl_language}</label>
    {include file="main/select/language.tpl" name="update_fields[basic][language]" value=$userinfo.language}
    {if $fill_error.basic.language}<font class="field_error">&lt;&lt;</font>{/if}
</div>
{/if}
==========================================================================
*}
</div>
{include file='admin/users/sections/custom.tpl'}
