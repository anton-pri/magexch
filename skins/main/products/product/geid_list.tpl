{if $products && $ge_id}
{capture name=section}
{include file='common/navigation.tpl'}

<table class="header" width="100%">
<tr>
    <th>{$lng.lbl_sku}</th>
    <th>{$lng.lbl_product}</th>
</tr>
{foreach from=$products item=v}
<tr{cycle name="ge" values=', class="cycle"'}>
    <td>
<a href="index.php?target=products&mode=details&product_id={$v.obj_id}{if $section ne 'main'}&amp;section={$section}{/if}&amp;ge_id={$ge_id}"{if $product_id eq $v.obj_id} class="selected"{/if}>{$v.productcode|escape}</a>
    </td>
    <td>
<a href="index.php?target=products&mode=details&product_id={$v.obj_id}{if $section ne 'main'}&amp;section={$section}{/if}&amp;ge_id={$ge_id}" {if $product_id eq $v.obj_id} class="selected"{/if}>{$v.product|escape}</a>
    </td>
</tr>
{/foreach}
</table>
{/capture}
{include file='common/section.tpl' content=$smarty.capture.section title=$lng.lbl_product_list}
{/if}
