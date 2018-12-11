<html>
{if $css_file ne ""}
<head>
	<link rel="stylesheet" href="{$SkinDir}/addons/estore_gift/{$css_file}" />
</head>
{/if}
<body>
{if $config.estore_gift.print_giftcerts_separated eq "Y"}
	{assign var="separator" value="<div style='page-break-after: always;'><!--[if IE 7]><br style='height: 0px; line-height: 0px;'><![endif]--></div>"}
{else}
	{assign var="separator" value="<br /><hr size='1' noshade='noshade' /><br />"}
{/if}
{foreach name=giftcerts from=$giftcerts key=key item=giftcert}
	{include file="addons/estore_gift/`$giftcert.tpl_file`"}
	{if not $smarty.foreach.giftcerts.last}
		{$separator}
	{/if}
{/foreach}
</body>
</html>
