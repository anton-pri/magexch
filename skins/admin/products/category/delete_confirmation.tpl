{capture name=block}
<div class="box">
{$lng.txt_delete_category_top_text}

<br /><br />

<span class="Text">
{$lng.txt_subcats_and_products_will_be_removed}
</span>
<br /><br />
<ul>
{if $subcats}
{section name=subcat loop=$subcats}
<li>{$subcats[subcat].category} {if $subcats[subcat].products_count gt 0} - {$lng.lbl_N_products|substitute:"products":$subcats[subcat].products_count}{/if}
{if $subcats[subcat].products}
<dl>
{section name=product loop=$subcats[subcat].products}
<dd><a href="index.php?target=products&mode=details&product_id={$subcats[subcat].products[product].product_id}" target="_blank">#{$subcats[subcat].products[product].product_id}. {$subcats[subcat].products[product].productcode} {$subcats[subcat].products[product].product}</a></dd>
{/section}
</dl>
{/if}
</li>
{/section}
</ul>
{/if}

{$lng.txt_operation_not_reverted_warning}

<br /><br />

<form action="index.php?target=categories" method="post" name="processform">
<input type="hidden" name="action" value="delete" />
<input type="hidden" name="confirmed" value="Y" />
<input type="hidden" name="cat" value="{$smarty.get.cat|escape:"html"}" />

<div class="buttons">
{$lng.txt_are_you_sure_to_proceed}&nbsp;&nbsp;&nbsp;
{include file='buttons/button.tpl' button_title=$lng.lbl_yes href="javascript:cw_submit_form('processform')" style='btn-green'}
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
{include file='buttons/button.tpl' button_title=$lng.lbl_no href="javascript:history.go(-1)" style='btn-green'}
</div>
</form>
</div>
{/capture}
{include file="admin/wrappers/block.tpl" content=$smarty.capture.block}

