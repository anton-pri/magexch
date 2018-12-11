<div style="padding: 5px;">
	<h3>History clean urls</h3>

	<div id="clean_urls_container">
		{if $clean_urls_list}
			{foreach from=$clean_urls_list item=url}
				{$url.url}&nbsp;
				<a href="javascript:delete_clean_url('{$url.id}');">
					<img src="{$current_location}/skins/images/delete_cross.gif" alt="Delete from history" title="Delete from history">
				</a><br>
			{/foreach}
		{else}
			No urls
		{/if}
	</div>
</div>


<script type="text/javascript">
<!--
{literal}	
	function delete_clean_url(id) {
		ajaxGet('index.php?target=clean_url_delete_url&url_id=' + id, 'clean_urls_container');
	}
{/literal}
-->
</script>
