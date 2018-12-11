{if $plans}

<tr>
	<td height="20" colspan="3"><b>{$lng.lbl_affiliate_plans}</b><hr size="1" noshade="noshade" /></td>
</tr>

{if $usertype eq "A" or ($usertype eq "P" and $addons.Simple_Mode ne "")}

<tr>
	<td align="right">{$lng.lbl_affiliate_plan}</td>
	<td>&nbsp;</td>
	<td nowrap="nowrap">
	<select name="plan_id">
		<option value=''>{$lng.lbl_none}</option>
{foreach from=$plans item=v}
		<option value="{$v.plan_id}"{if $userinfo.plan_id eq $v.plan_id} selected="selected"{/if}>{$v.plan_title}</option>
{/foreach}
	</select>
	</td>
</tr>

<tr>
    <td align="right">{$lng.lbl_max_discount_for_salesman}</td>
    <td>&nbsp;</td>
    <td nowrap="nowrap">
    <input type="text" name="max_discount" value="{$userinfo.max_discount}" />
    </td>
</tr>

<tr>
    <td align="right">{$lng.lbl_parent_profile}</td>
    <td>&nbsp;</td>
    <td nowrap="nowrap">
    <select name="parent">
    <option value="">{$lng.lbl_none}</option>
    {foreach from=$parent_profiles item=pp}
    <option value="{$pp.customer_id}"{if $userinfo.parent eq $pp.customer_id} selected{/if}>{$pp.firstname} {$pp.lastname}</option>
    {/foreach}
    </select>
    </td>
</tr>

{else}

<input type="hidden" name="plan_id" value="{$userinfo.plan_id}" />

{/if}

<tr>
	<td align="right">{$lng.lbl_signup_for_salesman_plan}</td>
	<td>&nbsp;</td>
	<td nowrap="nowrap">
	<select name="pending_plan_id">
		<option value=''>{$lng.lbl_none}</option>
{foreach from=$plans item=v}
		<option value="{$v.plan_id}"{if $userinfo.pending_plan_id eq $v.plan_id} selected="selected"{/if}>{$v.plan_title}</option>
{/foreach}
	</select>
	</td>
</tr>

{/if}
