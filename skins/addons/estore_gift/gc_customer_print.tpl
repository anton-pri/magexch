<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{$lng.lbl_preview|escape}</title>
	{if $css_file ne ""}
		<link rel="stylesheet" type="text/css" href="{$SkinDir}/addons/estore_gift/{$css_file}" />
	{/if}
	<meta http-equiv="Content-Type" content="text/html; charset={$default_charset|default:"iso-8859-1"}" />
	<meta http-equiv="X-UA-Compatible" content="IE=8" />
</head>
<body{$reading_direction_tag}>
	{if $config.estore_gift.print_giftcerts_separated eq "Y"}
		{assign var="separator" value="<div style='page-break-after: always;'><!--[if IE 7]><br style='height: 0px; line-height: 0px;' /><![endif]--></div>"}
	{else}
		{assign var="separator" value="<br /><hr size='1' noshade="noshade" /><br />"}
	{/if}
	{foreach name=giftcerts from=$giftcerts key=key item=giftcert}
		{include file="addons/estore_gift/`$giftcert.tpl_file`"}
		{if not $smarty.foreach.giftcerts.last}
			{$separator}
		{/if}
	{/foreach}
</body>
</html>
