<table class="header" width="100%">
<tr>
{if $accl.$page_acl}
    <th width="1%">&nbsp;</td>
{/if}
    <th width="1%">{$lng.lbl_address_main}</th>
    <th width="1%">{$lng.lbl_address_current}</th>
{if $profile_fields.address.title.is_avail || $profile_fields.address.firstname.is_avail || $profile_fields.address.lastname.is_avail}
    <th>{$lng.lbl_title}</th>
{/if}
{if $profile_fields.address.address.is_avail || $profile_fields.address.address_2.is_avail}
    <th>{$lng.lbl_address}</th>
{/if}
{if $profile_fields.address.city.is_avail}
    <th>{$lng.lbl_city}</th>
{/if}
{if $profile_fields.address.county.is_avail and $config.General.use_counties eq "Y"}
    <th>{$lng.lbl_county}</th>
{/if}
{if $profile_fields.address.state.is_avail}
    <th>{$lng.lbl_state}</th>
{/if}
{if $profile_fields.address.country.is_avail}
    <th>{$lng.lbl_country}</th>
{/if}
{if $profile_fields.address.zipcode.is_avail}
    <th>{$lng.lbl_zipcode}</th>
{/if}
{if $profile_fields.address.phone.is_avail}
    <th>{$lng.lbl_phone}</th>
{/if}
{if $profile_fields.address.fax.is_avail}
    <th>{$lng.lbl_fax}</th>
{/if}
    <th>&nbsp;</th>
</tr>
{if $addresses}
{foreach from=$addresses item=address}
<tr valign="top">
{if $accl.$page_acl}
    <td align="center"><input type="checkbox" name="del[{$address.address_id}]" value="1"></td>
{/if}
    <td align="center"><input type="radio" name="address_main" value="{$address.address_id}" {if $address.main}checked{/if}></td>
    <td align="center"><input type="radio" name="address_current" value="{$address.address_id}" {if $address.current}checked{/if}></td>
{if $profile_fields.address.title.is_avail || $profile_fields.address.firstname.is_avail || $profile_fields.address.lastname.is_avail}
    <td>
{if $profile_fields.address.title.is_avail}
        {$address.title}
{/if}
{if $profile_fields.address.firstname.is_avail}
    {$address.firstname}
{/if}
{if $profile_fields.address.lastname.is_avail}
{$address.lastname}
{/if}
    </td>
{/if}
{if $profile_fields.address.address.is_avail || $profile_fields.address.address_2.is_avail}
    <td>{$address.address}{if $address.address_2}<br>{$address.address_2}{/if}</td>
{/if}
{if $profile_fields.address.city.is_avail}
    <td>{$address.city}</td>
{/if}
{if $profile_fields.address.county.is_avail and $config.General.use_counties eq "Y"}
    <td>{$address.county}</td>
{/if}
{if $profile_fields.address.state.is_avail}
    <td>{$address.statename} {$address.state}</td>
{/if}
{if $profile_fields.address.country.is_avail}
    <td>{$address.countryname}</td>
{/if}
{if $profile_fields.address.zipcode.is_avail}
    <td>{$address.zipcode}</td>
{/if}
{if $profile_fields.address.phone.is_avail}
    <td>{$address.phone}</td>
{/if}
{if $profile_fields.address.fax.is_avail}
    <td>{$address.fax}</td>
{/if}
{assign var='custom_fields' value=$address.custom_fields}
{foreach from=$profile_fields.address item=field}
{if $field.type ne 'D'}
{assign var='fld_id' value=$field.field_id}
    <td>{$custom_fields.$fld_id}</td>
{/if}
{/foreach}
    <td><a href="index.php?target={$current_target}&mode={$mode}&user={$user}&address_id={$address.address_id}">{$lng.lbl_modify}</a></td>
</tr>
{/foreach}
{else}
<tr>
    <td align="center" colspan="13">{$lng.lbl_not_found}</td>
</tr>
{/if}
</table>
