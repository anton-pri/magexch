{if $attribute.field eq 'clean_url'}

{if $cat}
	{assign var="item_id" value=$cat}
	{assign var="item_type" value="C"}
{elseif $product_id}
	{assign var="item_id" value=$product_id}
	{assign var="item_type" value="P"}
{elseif $manufacturer_id}
	{assign var="item_id" value=$manufacturer_id}
	{assign var="item_type" value="M"}
{elseif $contentsection_id}
	{assign var="item_id" value=$contentsection_id}
	{assign var="item_type" value="AB"}
{else}
	{assign var="item_id" value=0}
	{assign var="item_type" value=""}
{/if}

<div><a onclick="return show_clean_url_history_dialog('{$item_id}', '{$item_type}')" href="" class="history_link">{$lng.lbl_history}</a></div>

<div id="clean_url_history_dialog" title="History"></div>

<script type="text/javascript">
{literal}
function show_clean_url_history_dialog(id, type){
	$("#clean_url_history_dialog").html('<iframe id="clean_url_history_iframe" width="100%" height="100%" marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto" />').dialog("open");
	$("#clean_url_history_iframe").attr("src", current_location + "/index.php?target=clean_url_show_history&item_id=" + id + "&item_type=" + type);
	return false;
}

$(document).ready(function() {
	$("#clean_url_history_dialog").dialog({
		autoOpen: false,
		modal	: true,
		height	: 400,
		width	: 720
	});
});
{/literal}
</script>
{/if}
