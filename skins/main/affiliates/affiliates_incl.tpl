{capture name=section}
<b>{$lng.lbl_note}:</b> 
{if $usertype ne 'B'}
{$lng.txt_affiliates_tree_comment_a}
{else}
{$lng.txt_affiliates_tree_comment_b}
{/if}
<table class="header" width="100%">
<tr>
    <th width="70%">{$lng.lbl_salesman}</th>
    <th>{$lng.lbl_commission}</hd>
    <th>{$lng.lbl_affiliate_commission}</th>
</tr>
<tr>
    <td>{$parent_affiliate.customer_id|user_title:'B'}</td>
    <td>{include file='common/currency.tpl' value=$parent_affiliate.sales|default:0}</td>
    <td>{include file='common/currency.tpl' value=$parent_affiliate.childs_sales}</td>
</tr>
<tr>
    <td>{include file="main/affiliates/list.tpl" affiliates=$affiliates level=0 type="1"}</td>
    <td>{include file="main/affiliates/list.tpl" affiliates=$affiliates level=0 type="2"}</td>
    <td>{include file="main/affiliates/list.tpl" affiliates=$affiliates level=0 type="3"}</td>
</tr>
</table>
{/capture}
{include file="common/section.tpl" content=$smarty.capture.section title=$lng.lbl_affiliates}
