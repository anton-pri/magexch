{if $top_menu eq ''}{tunnel func='top_menu_smarty_init' via='cw_call' assign='top_menu'}{/if}
<nav class="top">
<ul id="menu_top" style="display:none;">
{include file="addons/top_menu/items.tpl" items=$top_menu}
</ul>
</nav>

<div class="splitter"></div>

{literal}
<script type="text/javascript">
$(document).ready (function() { $('#menu_top').ptMenu(); $('#menu_top').css('display', ''); });
</script>
{/literal}