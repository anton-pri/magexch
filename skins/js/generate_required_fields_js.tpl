<script type="text/javascript" language="JavaScript 1.2">
<!--
var requiredFields = [
{foreach from=$fields item=v key=k}
{if $v.is_required && $v.is_avail && !$v.js_required_block}
	["{$k}", "{$v.title|strip|replace:'"':'\"'}"],
{/if}
{/foreach}
];
-->
</script>
