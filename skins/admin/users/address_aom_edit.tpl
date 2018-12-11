{if $profile_fields.address.title.is_avail}
<tr {cycle name=$name_prefix values=', class="cycle"'}>
    <td>{$lng.lbl_title}</td>
    <td nowrap="nowrap">
        {include file="main/select/user_title.tpl" name="`$name_prefix`[title]" field=$address.titleid}
    </td>
    <td>{$original.title}</td>
</tr>
{/if}

{if $profile_fields.address.firstname.is_avail}
<tr {cycle name=$name_prefix values=', class="cycle"'}>
    <td>{$lng.lbl_firstname}</td>
    <td nowrap="nowrap" class="form-inline">
        <input type="text" name="{$name_prefix}[firstname]" size="32" maxlength="32" value="{$address.firstname|escape}"{if $readonly} disabled{/if} class="form-control" />
    </td>
    <td>{$original.firstname}</td>
</tr>
{/if}

{if $profile_fields.address.lastname.is_avail}
<tr {cycle name=$name_prefix values=', class="cycle"'}>
    <td>{$lng.lbl_lastname}</td>
    <td nowrap="nowrap" class="form-inline">
        <input type="text" name="{$name_prefix}[lastname]" size="32" maxlength="32" value="{$address.lastname|escape}"{if $readonly} disabled{/if} class="form-control" />
    </td>
    <td>{$original.lastname}</td>
</tr>
{/if}

{if $profile_fields.address.address.is_avail}
<tr {cycle name=$name_prefix values=', class="cycle"'}>
    <td>{$lng.lbl_address}</td>
    <td nowrap="nowrap" class="form-inline">
        <input type="text" id="address_{$address.address_id}" name="{$name_prefix}[address]" size="32" maxlength="64" value="{$address.address|escape}"{if $readonly} disabled{/if} class="form-control" />
    </td>
    <td>{$original.address}</td>
</tr>
{/if}

{if $profile_fields.address.address_2.is_avail}
<tr {cycle name=$name_prefix values=', class="cycle"'}>
    <td>{$lng.lbl_address_2}</td>
    <td nowrap="nowrap" class="form-inline">
        <input type="text" id="address_2_{$address.address_id}" name="{$name_prefix}[address_2]" size="32" maxlength="64" value="{$address.address_2|escape}"{if $readonly} disabled{/if} class="form-control" />
    </td>
    <td>{$original.address_2}</td>
</tr>
{/if}

{if $profile_fields.address.country.is_avail}
<tr {cycle name=$name_prefix values=', class="cycle"'}>
    <td>{$lng.lbl_country}</td>
    <td nowrap="nowrap" class="form-inline">
    {include file="main/map/countries.tpl" name="`$name_prefix`[country]" default=$address.country region_name="`$name_prefix`[region]" region_value=$address.region state_name="`$name_prefix`[state]" state_value=$address.state county_name="`$name_prefix`[county]" county_value=$address.county region_enabled=$profile_fields.address.region.is_avail state_enabled=$profile_fields.address.state.is_avail }
    </td>
    <td>{$original.countryname}</td>
</tr>
{/if}

{if $profile_fields.address.region.is_avail}
<tr {cycle name=$name_prefix values=', class="cycle"'}>
    <td>{$lng.lbl_region}</td>
    <td nowrap="nowrap" class="form-inline">
    {include file="admin/select/regions.tpl" name="`$name_prefix`[region]" default=$address.region state_name="`$name_prefix`[state]" state_value=$address.state county_name="`$name_prefix`[county]" county_value=$address.county country_value=$address.country}

    </td>
    <td>{$original.regionname}</td>
</tr>
{/if}

{if $profile_fields.address.state.is_avail}
<tr {cycle name=$name_prefix values=', class="cycle"'}>
    <td>{$lng.lbl_state}</td>
    <td nowrap="nowrap" class="form-inline">
    {include file="main/map/states.tpl" name="`$name_prefix`[state]" default=$address.state county_name="`$name_prefix`[county]" county_value=$address.county city_name="`$name_prefix`[city]" city_value=$address.city}
    </td>
    <td>{$original.statename}</td>
</tr>
{/if}

{if $profile_fields.address.county.is_avail}
<tr {cycle name=$name_prefix values=', class="cycle"'}>
    <td>{$lng.lbl_county}</td>
    <td nowrap="nowrap" class="form-inline">
    {include file="admin/select/counties.tpl" name="`$name_prefix`[county]" default=$address.county}

    </td>
    <td>{$original.countyname}</td>
</tr>
{/if}

{if $profile_fields.address.city.is_avail}
<tr {cycle name=$name_prefix values=', class="cycle"'}>
    <td>{$lng.lbl_city}</td>
    <td nowrap="nowrap" class="cust_adress form-inline">
        {include file='admin/select/cities.tpl' name="`$name_prefix`[city]' value=$address.city}
    </td>
    <td>{$original.city}</td>
</tr>
{/if}

{if $profile_fields.address.zipcode.is_avail}
<tr {cycle name=$name_prefix values=', class="cycle"'}>
    <td>{$lng.lbl_zipcode}</td>
    <td nowrap="nowrap" class="form-inline">
        <input type="text" id="address[zipcode]" name="{$name_prefix}[zipcode]" size="32" maxlength="32" value="{$address.zipcode|escape}" onchange="check_zip_code()"{if $readonly} disabled{/if} class="form-control" />
    </td>
    <td>{$original.zipcode}</td>
</tr>
{/if}

{if $profile_fields.address.phone.is_avail}
<tr {cycle name=$name_prefix values=', class="cycle"'}>
    <td>{$lng.lbl_phone}</td>
    <td nowrap="nowrap" class="form-inline">
        <input type="text" id="phone" name="{$name_prefix}[phone]" size="32" maxlength="32" value="{$address.phone|escape}"{if $readonly} disabled{/if} class="form-control" />
    </td>
    <td>{$original.phone}</td>
</tr>
{/if}

{if $profile_fields.address.fax.is_avail}
<tr {cycle name=$name_prefix values=', class="cycle"'}>
    <td>{$lng.lbl_fax}</td>
    <td nowrap="nowrap" class="form-inline">
        <input type="text" id="fax" name="{$name_prefix}[fax]" size="32" maxlength="128" value="{$address.fax|escape}"{if $readonly} disabled{/if} class="form-control" />
    </td>
    <td>{$original.fax}</td>
</tr>
{/if}
