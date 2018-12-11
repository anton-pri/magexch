{if $config.Appearance.print_orders_separated eq "Y"}
{assign var="separator" value="<div style='page-break-after: always;'><!--[if IE 7]><br style='height: 0px; line-height: 0px;'><![endif]--></div>"}
{else}
{assign var="separator" value="<br /><hr size='1' noshade='noshade' /><br />"}
{/if}
{foreach from=$orders_data item=doc}

{include file='main/docs/doc_layout.tpl'}

{if not %oi.last%}
{$separator}
{/if}

{/section}
