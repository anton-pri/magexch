
<div class="top_menu">
<ul id="menu_top" style="display:none;">
{include file="addons/top_menu/items.tpl" items=$top_menu}
</ul>
</div>

<div class="splitter"></div>

{literal}
<script type="text/javascript">
$(document).ready (function() { $('#menu_top').ptMenu(); $('#menu_top').css('display', ''); });
</script>
{/literal}

