<div id="orders_chart_div" style="height: 300px; text-align: center;"></div>

<script type="text/javascript">
<!--
{literal}
	var graph_orders;
	$(document).ready(function(){
		$('body').bind('switch_to_tab', render_orders_graph);
	});

	function render_orders_graph(event, tab, contents, tab_name) {

		if (tab_name == 'orders' && ORDERS_CHART_GRAPH == null) {
			$('#orders_chart_div').html('');

			if (GRAPH_PERIOD < 2) {
				xaxis_format = "%H:%M";
			}
			else {
				xaxis_format = "%m/%d/%y";
			}

			var min = ORDERS_CHART_DATA[0][0];
			var max = ORDERS_CHART_DATA[ORDERS_CHART_DATA.length-1][0];

			// find max count
			var max_count = 0;
			for (var i = 0; i < ORDERS_CHART_DATA.length; i++) {

				if (max_count < ORDERS_CHART_DATA[i][1]) {
					max_count = ORDERS_CHART_DATA[i][1];
				}
			}

			setTimeout(function() {
				graph_orders = {
					"axes" : {
						"xaxis" : {
							"tickOptions":{
								"formatString" : xaxis_format,
                                "fontSize" : 10
						    },
							"min" : min,
							"max" : max,
							"numberTicks": 7,
							"pad": 1,
							"renderer" : $.jqplot.DateAxisRenderer
					    },
					    "yaxis" : {
					    	"tickOptions" : {
						    	"formatString" : '%d',
                                "fontSize" : 10
							},
							"min" : 0,
							"pad": 1
					    }
					},
					"animate" : false,
					"animateReplot" : false,
					"seriesDefaults" : {
						"fill" : true,
						"color" : "#92BC50"
					},
                    highlighter: {
                        show: true,
                        showLabel: true,
                        tooltipAxes: 'x',
                        sizeAdjust: 7.5 ,
                        tooltipLocation : 'ne'
                    }
				};

				if (max_count < 4 && max_count != 0) {
					graph_orders["axes"]["yaxis"]["tickInterval"] = 1;
				}
	
				$.jqplot.config.defaultHeight = 300;
				$.jqplot.config.defaultWidth  = 500;
				ORDERS_CHART_GRAPH = $.jqplot("orders_chart_div", [ORDERS_CHART_DATA], graph_orders);
	
			}, 100);
		}
	}
{/literal}
-->
</script>
{*
<div class="block-button text-right"><a href="javascript: graph_orders_search();" class="btn btn-minw btn-info">{$lng.lbl_search_orders}</a></div>
*}

