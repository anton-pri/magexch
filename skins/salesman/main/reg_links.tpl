{capture name=section}
<table class="header">
<tr>
    <th>{$lng.lbl_membership}</th>
    <th>{$lng.lbl_link}</th>
</tr>
{foreach from=$memberships item=membership}
<tr>
    <td>{$membership.membership}:</td>
    <td><input type="text" value="{$catalogs.customer}/index.php?target=acc_manager&saleman={$customer_id}&level={$membership.membership_id}" size="100"></td>
</tr>
{/foreach}
<tr>
    <td>{$lng.lbl_no_membership}:</td>
    <td><input type="text" value="{$catalogs.customer}/index.php?target=acc_manager&saleman={$customer_id}" size="100"></td>
</tr>
</table>
{/capture}
{include file="common/section.tpl" title=$lng.lbl_register_page_links content=$smarty.capture.section}

{capture name=section}
<table class="header">
<tr>
    <th>{$lng.lbl_membership}</th>
    <th>{$lng.lbl_link}</th>
</tr>
{foreach from=$memberships item=membership}
<tr>
    <td>{$membership.membership}:</td>
    <td><input type="text" value="{$catalogs.customer}/index.php?saleman={$customer_id}&level={$membership.membership_id}" size="100"></td>
</tr>
{/foreach}
<tr>
    <td>{$lng.lbl_no_membership}:</td>
    <td><input type="text" value="{$catalogs.customer}/index.php?saleman={$customer_id}" size="100"></td>
</tr>
</table>
{/capture}
{include file="common/section.tpl" title=$lng.lbl_home_page_links content=$smarty.capture.section}

{capture name=section}
<table class="header">
<tr>
    <th>{$lng.lbl_membership}</th>
    <th>{$lng.lbl_link}</th>
</tr>
{foreach from=$resellers_memberships item=membership}
<tr>
    <td>{$membership.membership}:</td>
    <td><input type="text" value="{$catalogs.customer}/index.php?target=acc_manager&saleman={$customer_id}&level={$membership.membership_id}" size="100"></td>
</tr>
{/foreach}
</table>
{/capture}
{include file="common/section.tpl" title=$lng.lbl_register_reseller_page_links content=$smarty.capture.section}

{capture name=section}
<table class="header">
<tr>
    <th>{$lng.lbl_membership}</th>
    <th>{$lng.lbl_link}</th>
</tr>
{foreach from=$resellers_memberships item=membership}
<tr>
    <td>{$membership.membership}:</td>
    <td><input type="text" value="{$catalogs.customer}/index.php?saleman={$customer_id}&level={$membership.membership_id}" size="100"></td>
</tr>
{/foreach}
</table>
{/capture}
{include file="common/section.tpl" title=$lng.lbl_home_reseller_page_links content=$smarty.capture.section}
