<tr>
<td colspan="3" class="RegSectionTitle">{if $section_name}{$section_name}{else}{$lng.lbl_contactus}{/if}<hr size="1" noshade="noshade" /></td>
</tr>

{if $profile_fields.department.avail eq 'Y'}
<tr valign="middle">
<td class="FormButton">{$lng.lbl_department}</td>
<td>{if $profile_fields.department.required eq 'Y'}<font class="Star">*</font>{/if}</td>
<td nowrap="nowrap">
<select id="department" name="department">
<option value="All" selected="selected">{$lng.lbl_all}</option>
<option value="Partners">{$lng.lbl_salesmans}</option>
<option value="Marketing / publicity">{$lng.lbl_marketing_publicity}</option>
<option value="Webdesign">{$lng.lbl_web_design}</option>
<option value="Sales">{$lng.lbl_sales_department}</option>
</select>
</td>
</tr>
{/if}

{if $profile_fields.title.avail eq 'Y'}
<tr valign="middle">
<td class="FormButton">{$lng.lbl_title}</td>
<td>{if $profile_fields.title.required eq 'Y'}<font class="Star">*</font>{/if}</td>
<td nowrap="nowrap">
<select id="title" name="title">
{include file="main/select/user_title.tpl" field=$userinfo.titleid}
</select>
</td>
</tr>
{/if}

{if $profile_fields.firstname.avail eq 'Y'}
<tr valign="middle">
<td class="FormButton">{$lng.lbl_firstname}</td>
<td>{if $profile_fields.firstname.required eq 'Y'}<font class="Star">*</font>{/if}</td>
<td nowrap="nowrap">
<input type="text" id="firstname" name="firstname" size="32" maxlength="32" value="{$userinfo.firstname}" />
{if $fillerror ne "" and $userinfo.firstname eq "" && $profile_fields.firstname.required eq 'Y'}<font class="Star">&lt;&lt;</font>{/if}
</td>
</tr>
{/if}

{if $profile_fields.lastname.avail eq 'Y'}
<tr valign="middle">
<td class="FormButton">{$lng.lbl_lastname}</td>
<td>{if $profile_fields.lastname.required eq 'Y'}<font class="Star">*</font>{/if}</td>
<td nowrap="nowrap">
<input type="text" id="lastname" name="lastname" size="32" maxlength="32" value="{$userinfo.lastname}" />
{if $fillerror ne "" and $userinfo.lastname eq "" && $profile_fields.lastname.required eq 'Y'}<font class="Star">&lt;&lt;</font>{/if}
</td>
</tr>
{/if}

{if $profile_fields.company.avail eq 'Y'}
<tr valign="middle">
<td class="FormButton">{$lng.lbl_company}</td>
<td>{if $profile_fields.company.required eq 'Y'}<font class="Star">*</font>{/if}</td>
<td nowrap="nowrap">
<input type="text" id="company" name="company" size="32" value="{$userinfo.company}" />
{if $fillerror ne "" and $userinfo.company eq "" && $profile_fields.company.required eq 'Y'}<font class="Star">&lt;&lt;</font>{/if}
</td>
</tr>
{/if}

{if $profile_fields.b_address.avail eq 'Y'}
<tr valign="middle">
<td class="FormButton">{$lng.lbl_address}</td>
<td>{if $profile_fields.b_address.required eq 'Y'}<font class="Star">*</font>{/if}</td>
<td nowrap="nowrap">
<input type="text" id="b_address" name="b_address" size="32" maxlength="64" value="{$userinfo.b_address}" />
{if $fillerror ne "" and $userinfo.b_address eq "" && $profile_fields.b_address.required eq 'Y'}<font class="Star">&lt;&lt;</font>{/if}
</td>
</tr>
{/if}

{if $profile_fields.b_address_2.avail eq 'Y'}
<tr valign="middle">
<td class="FormButton">{$lng.lbl_address_2}</td>
<td>{if $profile_fields.b_address_2.required eq 'Y'}<font class="Star">*</font>{/if}</td>
<td nowrap="nowrap">
<input type="text" id="b_address_2" name="b_address_2" size="32" maxlength="64" value="{$userinfo.b_address_2}" />
{if $fillerror ne "" and $userinfo.b_address_2 eq "" && $profile_fields.b_address_2.required eq 'Y'}<font class="Star">&lt;&lt;</font>{/if}
</td>
</tr>
{/if}

{if $profile_fields.b_city.avail eq 'Y'}
<tr valign="middle">
<td class="FormButton">{$lng.lbl_city}</td>
<td>{if $profile_fields.b_city.required eq 'Y'}<font class="Star">*</font>{/if}</td>
<td nowrap="nowrap">
<input type="text" id="b_city" name="b_city" size="32" maxlength="64" value="{$userinfo.b_city}" />
{if $fillerror ne "" and $userinfo.b_city eq "" && $profile_fields.b_city.required eq 'Y'}<font class="Star">&lt;&lt;</font>{/if}
</td>
</tr>
{/if}

{if $profile_fields.b_county.avail eq 'Y' && $config.General.use_counties eq "Y"}
<tr valign="middle">
<td class="FormButton">{$lng.lbl_county}</td>
<td>{if $profile_fields.b_county.required eq 'Y'}<font class="Star">*</font>{/if}</td>
<td nowrap="nowrap">
{include file="main/counties.tpl" counties=$counties name="b_county" default=$userinfo.b_county state_id=$userinfo.b_state_id country_name="b_country"}
</td>
</tr>
{/if}

{if $profile_fields.b_state.avail eq 'Y'}
<tr valign="middle">
<td class="FormButton">{$lng.lbl_state}</td>
<td>{if $profile_fields.b_state.required eq 'Y'}<font class="Star">*</font>{/if}</td>
<td nowrap="nowrap">
{include file="main/states.tpl" states=$states name="b_state" default=$userinfo.b_state default_country=$userinfo.b_country country_name="b_country"}
</td>
</tr>
{/if}

{if $profile_fields.b_country.avail eq 'Y'}
<tr valign="middle">
<td class="FormButton">{$lng.lbl_country}</td>
<td>{if $profile_fields.b_country.required eq 'Y'}<font class="Star">*</font>{/if}</td>
<td nowrap="nowrap">
<select id="b_country" name="b_country" onchange="javascript: check_zip_code();">
{section name=country_idx loop=$countries}
<option value="{$countries[country_idx].country_code}" {if $userinfo.b_country eq $countries[country_idx].country_code}selected{elseif $countries[country_idx].country_code eq $config.General.default_country and $userinfo.b_country eq ""}selected{/if}>{$countries[country_idx].country}</option>
{/section}
</select>
</td>
</tr>
{/if}

{if $config.General.use_js_states eq 'Y' && $profile_fields.b_state.avail eq 'Y' && $profile_fields.b_country.avail eq 'Y'}
<tr style="display: none;">
    <td>
{include file="change_states_js.tpl"}
{include file="main/register_states.tpl" state_name="b_state" country_name="b_country" county_name="b_county" state_value=$userinfo.b_state county_value=$userinfo.b_county}
    </td>
</tr>
{/if}

{if $profile_fields.b_zipcode.avail eq 'Y'}
<tr valign="middle">
<td class="FormButton">{$lng.lbl_zipcode}</td>
<td>{if $profile_fields.b_zipcode.required eq 'Y'}<font class="Star">*</font>{/if}</td>
<td nowrap="nowrap">
<input type="text" id="b_zipcode" name="b_zipcode" size="32" maxlength="32" value="{$userinfo.b_zipcode}" onchange="javascript: check_zip_code(document.getElementById('b_country'), this);" />
{if $fillerror ne "" and $userinfo.b_zipcode eq "" && $profile_fields.b_zipcode.required eq 'Y'}<font class="Star">&lt;&lt;</font>{/if}
</td>
</tr>
{/if}

{if $profile_fields.phone.avail eq 'Y'}
<tr valign="middle">
<td class="FormButton">{$lng.lbl_phone}</td>
<td>{if $profile_fields.phone.required eq 'Y'}<font class="Star">*</font>{/if}</td>
<td nowrap="nowrap">
<input type="text" id="phone" name="phone" size="32" maxlength="32" value="{$userinfo.phone}" />
{if $fillerror ne "" and $userinfo.phone eq "" && $profile_fields.phone.required eq 'Y'}<font class="Star">&lt;&lt;</font>{/if}
</td>
</tr>
{/if}

{if $profile_fields.email.avail eq 'Y'}
<tr valign="middle">
<td class="FormButton">{$lng.lbl_email}</td>
<td>{if $profile_fields.email.required eq 'Y'}<font class="Star">*</font>{/if}</td>
<td nowrap="nowrap">
<input type="text" id="email" name="email" size="32" maxlength="128" value="{$userinfo.email}" onchange="javascript: checkEmailAddress(this);" />
{if $fillerror ne "" and $userinfo.email eq "" && $profile_fields.email.required eq 'Y'}<font class="Star">&lt;&lt;</font>{/if}
</td>
</tr>
{/if}

{if $profile_fields.fax.avail eq 'Y'}
<tr valign="middle">
<td class="FormButton">{$lng.lbl_fax}</td>
<td>{if $profile_fields.fax.required eq 'Y'}<font class="Star">*</font>{/if}</td>
<td nowrap="nowrap">
<input type="text" id="fax" name="fax" size="32" maxlength="128" value="{$userinfo.fax}" /></td>
</tr>
{/if}

{if $profile_fields.url.avail eq 'Y'}
<tr valign="middle">
<td class="FormButton">{$lng.lbl_web_site}</td>
<td>{if $profile_fields.url.required eq 'Y'}<font class="Star">*</font>{/if}</td>
<td nowrap="nowrap">
<input type="text" id="url" name="url" size="32" maxlength="128" value="{if $userinfo.url eq ""}http://{else}{$userinfo.url}{/if}" />
{if $fillerror ne "" and $userinfo.url eq "" && $profile_fields.url.required eq 'Y'}<font class="Star">&lt;&lt;</font>{/if}
</td>
</tr>
{/if}
