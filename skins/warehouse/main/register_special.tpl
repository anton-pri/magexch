{if $usertype eq "A"}

{if $hide_header eq ""}
<tr>
<td colspan="3" class="RegSectionTitle">{$lng.lbl_companies}<hr size="1" noshade="noshade" /></td>
</tr>
{/if}

<tr valign="middle">
<td align="right">{$lng.lbl_company}</td>
    <td>&nbsp;</td>
    <td nowrap="nowrap">
        <select name="companies[]" multiple size="7">
            {assign var="user_companies" value=$userinfo.companies}
            {foreach from=$possible_companies item=company}
            {assign var="company_id" value=$company.company_id}
            <option value="{$company_id}"{if $user_companies.$company_id} selected="selected"{/if}>{$company.company_name}</option>
            {/foreach}
            </select>
    </td>
</tr>

{/if}
