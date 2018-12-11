<div class="block">
	<ul class="nav nav-tabs nav-tabs-alt" data-toggle="tabs" id="flexible-tabs">
		<li>
			<a href="{$catalogs.admin}/index.php?target=datahub_raw_import">Initial Feeds Import</a>
		</li>
		<li>
			<a href="{$catalogs.admin}/index.php?target=datahub_buffer_match">Match Imported Items</a>
		</li>
		<li>
			<a href="{$catalogs.admin}/index.php?target=datahub_main_edit">Edit Hub Items</a>
		</li>
                <li class="pull-right">
                        <a href="{$catalogs.admin}/index.php?target=datahub_tools">Tools</a>
                </li>
                <li class="pull-right">
                        <a href="{$catalogs.admin}/index.php?target=datahub_rnt">Print Tags</a>
                </li>
		<li class="pull-right">
			<a href="{$catalogs.admin}/index.php?target=datahub_configuration" data-toggle="tooltip" title="" data-original-title="Settings"><i class="si si-settings"></i> Configuration</a>
		</li>
	</ul>
</div>

<script>
$(document).ready(function(){ldelim}
	var active = {$active};
	$('#flexible-tabs li:eq(' + active + ')').addClass('active');
{rdelim});
</script>
