<div class="block">
	<ul class="nav nav-tabs nav-tabs-alt" data-toggle="tabs" id="flexible-tabs">
		<li>
			<a href="{$catalogs.admin}/index.php?target=datahub_raw_import">Initial Feeds Import</a>
		</li>
                <li>
                        <a href="{$catalogs.admin}/index.php?target=datahub_interim_buffer_match">Interim Import Buffer</a>
                </li>
		<li>
			<a href="{$catalogs.admin}/index.php?target=datahub_buffer_match">Working Import Buffer</a>
		</li>
		<li>
			<a href="{$catalogs.admin}/index.php?target=datahub_main_edit">Edit Hub Items</a>
		</li>

                <li class="pull-right">
                        <a href="{$catalogs.admin}/index.php?target=datahub_tools"></a>
                </li>
                <li class="pull-right">
                        <a href="{$catalogs.admin}/index.php?target=datahub_rnt">Print Tags</a>
                </li>
		<li class="pull-right">
			<a href="{$catalogs.admin}/index.php?target=datahub_configuration" data-toggle="tooltip" title="" data-original-title="Settings"><i class="si si-settings"></i> Configuration</a>
		</li>
                <li class="pull-right">
                        <a href="{$catalogs.admin}/index.php?target=datahub_products_backups">Products Backups</a>
                </li>
                <li class="pull-right">
                        <a href="{$catalogs.admin}/index.php?target=datahub_price_levels">Price Calc Levels</a>
                </li>

	</ul>
</div>

<script>
$(document).ready(function(){ldelim}
	var active = {$active};
	$('#flexible-tabs li:eq(' + active + ')').addClass('active');
{rdelim});
</script>
