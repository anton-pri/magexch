<div class="dialog_title">{$lng.lbl_product_delete_confirmation_header}:</div>

<form action="index.php?target={$current_target}" method="post" name="process_form">
<input type="hidden" name="mode" value="process" />
<input type="hidden" name="action" value="" />
<input type="hidden" name="confirmed" value="Y" />

<ul>
{section name=prod loop=$products}
<li>{$products[prod].productcode} {$products[prod].product} - {include file='common/currency.tpl' value=$products[prod].price}
{$products[prod].category}
</li>
{/section}
</ul>

<br />

{$lng.txt_operation_not_reverted_warning}

<br /><br />
{$lng.txt_are_you_sure_to_proceed}
{include file="buttons/yes.tpl" href="javascript: cw_submit_form('process_form', 'delete')"}
{include file="buttons/no.tpl" href="index.php?target=`$current_target`"}
</form>
