{*include file='main/docs/doc_layout.tpl'*}
{assign var="separator" value="<div style='page-break-after: always;'><!--[if IE 7]><br style='height: 0px; line-height: 0px;' /><![endif]--></div>"}
{foreach from=$slips item=slip name=slips}
{$slip}
        {if not $smarty.foreach.slips.last}
                {$separator}
        {/if}
{/foreach}

{literal}
<style>
@page {
  size: A4;
}
@media print {
  slip {
    width: 210mm;
    height: 297mm;
  }
}
</style>

<script type='text/javascript'>
window.print();
</script>

{/literal}

