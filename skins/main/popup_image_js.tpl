{* $id$ *}
<script type="text/javascript">
<!--

{literal}
function popup_image(type, id, max_x, max_y) {

	if (!max_x)
		max_x = screen.width;
	else
		max_x += 10;
	if (!max_y)
		max_y = screen.height;
	else
		max_y += 10;
	return window.open('popup_index.php?target=image&type='+type+'&id='+id,'images','width='+max_x+',height='+max_y+',toolbar=no,status=no,scrollbars=yes,resizable=yes,menubar=no,location=no,direction=no');
}
{/literal}
-->
</script>
