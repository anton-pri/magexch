{if $included_tab eq '1'}
{* start *}
    <div class="product_description">{if $product.fulldescr ne ""}{$product.fulldescr}{else}{$product.descr}{/if}</div>


{elseif $included_tab eq 3}
{* start *}
{$product.specifications}

{elseif $included_tab eq 6}
{* start *}
{$product.warranties}
<script language="Javascript">
{literal}
function print_agreement(product_id, mode) {
    window.open('index.php?target=popup_agreement&product_id='+product_id+'&mode='+mode, 'popup_agreement', 'width=700,height=300,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no');
}
{/literal}
</script>
{include file='buttons/button.tpl' button_title=$lng.lbl_print_agreement href="javascript: print_agreement('`$product.product_id`', '');" style='btn'}
{include file='buttons/button.tpl' button_title=$lng.lbl_print_agreement_in_pdf href="javascript: print_agreement('`$product.product_id`', 'pdf'); void(0);" style='btn'}

{elseif $included_tab eq 7}
{* start *}
{capture name="border_box1"}<table cellspacing="2" cellpadding="0" width="100%">
<tr>
<td class="DialogTitleLite"><img src="{$ImagesDir}/tab/rarrow.gif" border="0"> {$product.product}</td></tr>
</table>{/capture}
{include file="common/section.tpl" content=$smarty.capture.border_box1 cellpadding="0" color="lite_grey"}
{include file="customer/main/product_faq.tpl}&nbsp;

{/if}
