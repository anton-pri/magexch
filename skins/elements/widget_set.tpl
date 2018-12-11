{* Export objects set *}

<script type='text/javascript'>
{literal}
	$(document).ready(function() {
		ajaxGet('index.php?target=import&mode=export_set&action=getcount');
	});

	function resetSet() {
		ajaxGet('index.php?target=import&mode=export_set&action=reset', 'widget_set_container');
	}
{/literal}
</script>

<div id="widget_set_container">{$lng.lbl_all_products}</div>
