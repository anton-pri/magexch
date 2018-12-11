<tr valign="middle">
<td align="right">{$lng.lbl_membership}</td>
<td>{if $usertype eq "A" or $usertype eq "P"}<font class="Star">*</font>{else}&nbsp;{/if}</td>
<td nowrap="nowrap">
<select name="membershipid" {if $readonly}disabled{/if}>
<option value="">{$lng.lbl_not_member}</option>
{foreach from=$membership_levels item=v}
<option value="{$v.membershipid}"{if $userinfo.membershipid eq $v.membershipid} selected="selected"{/if}>{$v.membership}</option>
{/foreach}
</select>
{if ($usertype eq "A" or $usertype eq "P") and $userinfo.membershipid eq 0 and $reg_error ne ""}<font class="Star">&lt;&lt;</font>{/if}
</td>
</tr>
