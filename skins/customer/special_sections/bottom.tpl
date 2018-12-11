{if $bottom_line}
{capture name=section}
{include file='customer/products/products_b.tpl' products=$bottom_line}
{/capture} 
{include file='common/section.tpl' title=$lng.lbl_bottom_line_specials content=$smarty.capture.section style='bottom' is_dialog=1}
{/if}
