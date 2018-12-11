<style type="text/css" media="all">
.barcode_layout {ldelim}
    position: relative;
    overflow: hidden;
    margin: {$layout.data.top*$hcr|round|default:0}px {$layout.data.right*$wcr|round|default:0}px {$layout.data.bottom*$hcr|round|default:0}px {$layout.data.left*$wcr|round|default:0}px;
    height: {$layout.data.height*$hcr|round|default:0}px;
    width: {$layout.data.width*$wcr|round|default:0}px;
    white-space: nowrap;
border: 1px solid red;
{if $layout.data.border}
    border: 1px solid black;
{/if}
{rdelim}
{* because the printer margin + html2pdf feature *}
.barcode_page {ldelim}
    padding: {$page_margin.0*$hcr|round|default:0}px {$page_margin.1*$wcr|round|default:0}px {$page_margin.2*$hcr|round|default:0}px {$page_margin.3*$wcr|round|default:0}px;
    width: 1024px;
    height: 1300px;
    overflow: hidden;
{rdelim}
</style>
{include file='main/layout/css.tpl' layout_id=$layout.layout_id}
{assign var="index" value=0}
{section name=page loop=$layout.data.pages}
<div class="barcode_page">
<table cellspacing="0" cellpadding="0">
{section name=row loop=$layout.data.rows}
<tr>
{section name=col loop=$layout.data.cols}
<td>
<div class="barcode_layout">
{if $products.$index && $smarty.section.row.index+1 >= $options.rows_from && $smarty.section.row.index+1 <= $options.rows_to && $smarty.section.col.index+1 >= $options.cols_from && $smarty.section.col.index+1 <= $options.cols_to}
{include file='addons/barcode/layout.tpl' product=$products.$index}
{math equation="a+1" a=$index assign="index"}
{/if}
</div>
</td>
{/section}
</tr>
{/section}
</table>
</div>
{/section}
