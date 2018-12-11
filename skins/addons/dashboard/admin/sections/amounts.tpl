<div id="amounts_chart_div" style="height: 300px; text-align: center;"></div>

<script type="text/javascript">
<!--
{literal}
	var graph_amounts;
	$(document).ready(function(){
		$('body').bind('switch_to_tab', render_amounts_graph);
	});

	function render_amounts_graph(event, tab, contents, tab_name, chart_data) {

		if (tab_name == 'amounts' && AMOUNTS_CHART_GRAPH == null) {
			$('#amounts_chart_div').html('');

			if (GRAPH_PERIOD < 2) {
				xaxis_format = "%H:%M";
			}
			else {
				xaxis_format = "%m/%d/%y";
			}

			var min = AMOUNTS_CHART_DATA[0][0];
			var max = AMOUNTS_CHART_DATA[AMOUNTS_CHART_DATA.length-1][0];

			setTimeout(function() {
				graph_amounts = {
					"axes" : {
						"xaxis" : {
							"tickOptions" : {
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
						    	"formatString" : currency_symbol + '%.2f',
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

				$.jqplot.config.defaultHeight = 300;
				$.jqplot.config.defaultWidth  = 500;
				AMOUNTS_CHART_GRAPH = $.jqplot("amounts_chart_div", [AMOUNTS_CHART_DATA], graph_amounts);
	
			}, 100);
		}
	}
{/literal}
-->
</script>

