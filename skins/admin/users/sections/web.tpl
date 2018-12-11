<div class="input_field_1">
    <label class='required'>{$lng.lbl_password}</label>
    <input type="password" class='required' name="update_fields[web][passwd1]" id='passwd1' maxlength="64" value="{$userinfo.passwd1}" />
    {if $fill_error.web.password}<font class="field_error">&lt;&lt;</font>{/if}
{if $current_area eq 'A'}
    <input type="checkbox" name="update_fields[web][change_password]" value="1"{if $userinfo.change_password} checked="checked"{/if} />
    {$lng.lbl_reg_chpass}
{/if}
</div>
<div class="input_field_1">
    <label class='required'>{$lng.lbl_confirm_password}</label>
    <input type="password" class='requred' equalTo='#passwd1' name="update_fields[web][passwd2]" maxlength="64" value="{$userinfo.passwd2}" />
    {if $fill_error.web.password}<font class="field_error">&lt;&lt;</font>{/if}
</div>
{include file='main/users/sections/custom.tpl' included_tab='web'}
