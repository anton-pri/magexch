<div class="block bg-gray-lighter">

{jstabs}

[orders]
title="{$lng.lbl_orders}"
template="addons/dashboard/admin/sections/orders.tpl"

[amounts]
title="{$lng.lbl_amounts}"
template="addons/dashboard/admin/sections/amounts.tpl"

{/jstabs}

<span style="float: right; margin-top: 11px; margin-right: 10px;">
<select onchange="render_graph_by_period(this.value);return false;" id="period_selector">
<option value="0">Today</option>
<option value="1">Yesterday</option>
<option value="2">Last 7 days</option>
<option value="3">Last 30 days</option>
<option value="4">Current month</option>
</select>
</span>

{include file='admin/tabs/js_tabs.tpl' name="graph_sections"}

<div class="order_diagramm block-content text-center">
  <div class="row items-push text-center">
    <div class="col-xs-4">
      <div class="push-10">
        <i class="si si-graph fa-2x"></i>
      </div>
      <div class="h5 font-w300 text-muted">
        {$lng.lbl_order_count} - <span id="graph_orders_count"></span>
      </div>
    </div>
    <div class="col-xs-4">
      <div class="push-10">
        <i class="si si-users fa-2x"></i>
      </div>
      <div class="h5 font-w300 text-muted">
        {$lng.lbl_revenue} - <span id="graph_orders_amount"></span>
      </div>
    </div>
    <div class="col-xs-4">
      <div class="push-10">
        <i class="si si-share fa-2x"></i>

      </div>
      <div class="h5 font-w300 text-muted">
	 {$lng.lbl_margin} - <span id="graph_orders_margin"></span>
      </div>
    </div>

  </div>
</div>

<form action="index.php?target=docs_O&sort=date" id='search_by_founded_orders' method='POST'>
    <input type='hidden' name='posted_data[basic][creation_date_start]' id='search_by_founded_orders_start' />
    <input type='hidden' name='posted_data[basic][creation_date_end]' id='search_by_founded_orders_end' />
    <input type="hidden" name="action" value="search" />
    <input type="hidden" name="search_sections[tab_search_orders]" value="1" />
</form>

<script language="JavaScript">
var currency_symbol = '{$config.General.currency_symbol}';

var images_dir = '{$ImagesDir}';

var graph_orders_date = [];

ORDERS_CHART_GRAPH 	= null;
AMOUNTS_CHART_GRAPH = null;

ORDERS_CHART_DATA 	= [];
AMOUNTS_CHART_DATA 	= [];

GRAPH_PERIOD = 0;

{literal}
	$(document).ready(function() {
		render_graph_by_period(0);
	});

	function render_graph_by_period(val) {
		var data = {};
		data["type"] 	= 'graph';
		data["period"] 	= val;
		GRAPH_PERIOD	= val;

		$('#period_selector').prop('disabled', 'disabled');

		// add loader to active tab
		if (previous_tabs[''] == 'tab_amounts') {
			$('#amounts_chart_div').html('<img src="' + images_dir + '/loading.gif">');
		}
		else {
			$('#orders_chart_div').html('<img src="' + images_dir + '/loading.gif">');
		}

		$.ajax({
			url		: "index.php?target=quick_data",
			type	: "POST",
			dataType: "json",
			data	: data,
			success	: function(data) {
				$('#period_selector').prop('disabled', '');

				ORDERS_CHART_GRAPH 	= null;
				AMOUNTS_CHART_GRAPH = null;
				ORDERS_CHART_DATA 	= [];
				AMOUNTS_CHART_DATA 	= [];

				var orders_count = 0;
				var orders_amount = 0;
				var orders_margin = 0;

				$.each(data, function(index, item) {
					var graph_element = [];
					graph_element[0] = item.date;
					graph_element[1] = item.count;
					ORDERS_CHART_DATA.push(graph_element);
					orders_count += item.count;

					graph_element = [];
					graph_element[0] = item.date;
					graph_element[1] = item.amount;
					AMOUNTS_CHART_DATA.push(graph_element);
					orders_amount += item.amount;
					orders_margin += item.margin;

					if (index == 0) {
						graph_orders_date[0] = item.timestamp;
					}

					if (index == data.length-1) {
						graph_orders_date[1] = item.timestamp;
					}
				});

				$('#graph_orders_count').html('<a href="javascript: graph_orders_search();">' + orders_count + '</a>');
				$('#graph_orders_amount').html('<a href="javascript: graph_orders_search();">' + currency_symbol + orders_amount.toFixed(2) + '</a>');
				$('#graph_orders_margin').html('<a href="javascript: graph_orders_search();">' + currency_symbol + orders_margin.toFixed(2) + '</a>');

				// render active tab
				if (previous_tabs[''] == 'tab_amounts') {
					render_amounts_graph(null, null, null, 'amounts');
				}
				else {
					render_orders_graph(null, null, null, 'orders');
				}
			}
		});
	}

	function graph_orders_search() {
	    $('#search_by_founded_orders_start').val(graph_orders_date[0]);
	    $('#search_by_founded_orders_end').val(graph_orders_date[1]);
	    $('#search_by_founded_orders').submit();
	    return false;
	}
{/literal}
</script>
</div>

<div class="block-button text-right"><a href="javascript: graph_orders_search();" class="btn btn-minw btn-info">{$lng.lbl_search_orders}</a></div>
