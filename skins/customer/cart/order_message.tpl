{if $this_is_printable_version eq ''}
<!-- cw@order_confirmation [ -->
{capture name=section}
<font class="ProductDetails">{$lng.txt_order_placed}</font>
<br /><br />
<font class="ProductDetails">{$lng.txt_order_placed_msg}</font>
<br />
{/capture}
{include file="common/section.tpl" title=$lng.lbl_confirmation content=$smarty.capture.section}
<!-- cw@order_confirmation ] -->
{/if}
{assign var='doc_title' value=$lng.lbl_doc_info_O}
{*capture name=section*}
{foreach from=$orders item=order}
<!-- cw@order_details [ -->
{include file='main/docs/doc_layout.tpl' doc=$order}
<!-- cw@order_details ] -->

{if $addons.interneka}
{include file='addons/interneka/interneka_tags.tpl'} 
{/if}
{if $order.type eq "I"}
{assign var='doc_title' value=$lng.lbl_doc_info_I}
{/if}
{/foreach}
<div class='clear'></div>
{if $this_is_printable_version eq ''}
<!-- cw@order_buttons [ -->
<div class="order_buttons">
<div class="left_floated">{include file='buttons/button.tpl' button_title=$lng.lbl_print_order href="index.php?target=docs_O&amp;mode=print&doc_id=`$doc_ids`" target="preview_invoice"}</div>
<div class="right_floated">{include file='buttons/button.tpl' button_title=$lng.lbl_continue_shopping style='btn' href='index.php'}</div>
<div class="clear"></div>
</div>
<!-- cw@order_buttons ] -->

{/if}

{*/capture}
{include file='common/section.tpl' title=$doc_title content=$smarty.capture.section*}
