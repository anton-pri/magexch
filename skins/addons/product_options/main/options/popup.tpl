<script type="text/javascript">
<!--
var min_avail = {$min_avail|default:0};
var avail = {math equation="x+1" x=$min_avail};
var product_avail = avail;
var txt_out_of_stock = "{$lng.txt_out_of_stock}";

{literal}
function FormValidationEdit() {

  if(!check_exceptions()) {
    alert(exception_msg);
    return false;

  } else if (min_avail > avail) {
    alert(txt_out_of_stock);
    return false;
	}

{/literal}
	{if $product_options_js ne ''}
	{$product_options_js}
	{/if}
{literal}

    return true;
}
{/literal}
-->
</script>
<div class="prod_options">
<form action="index.php?target={$current_target}&id={$id}" method="post" name="change_options_frm" onsubmit="javascript: return FormValidationEdit();">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="mode" value="{$mode}" />
<input type="hidden" name="eventid" value="{$eventid}" />


{include file="addons/product_options/customer/products/product-amount.tpl"}

{include file="buttons/button.tpl" button_title=$lng.lbl_update href="javascript: cw_submit_form('change_options_frm');" style='button'}

</form>
<div class="clear"></div>
</div>
