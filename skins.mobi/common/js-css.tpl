{if $include.css}
{foreach from=$include.css item=file}
<link rel="stylesheet" href="{$SkinDir}/{$file}" type="text/css" media="all" />
{/foreach}
<!--[if lte IE 7]>
<link href="{$SkinDir}/ie7.css" rel="stylesheet" type="text/css" />
<![endif]-->
{/if}
{if $include.js}
{foreach from=$include.js item=file}
<script src="{$SkinDir}/{$file}" type="text/javascript"></script>
{/foreach}
{/if}
