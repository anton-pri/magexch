        function initPanZoom() {
          $('#pan img').panZoom({
            'zoomIn'   	: 		$('#zoomin'),
            'zoomOut' 	: 		$('#zoomout'),
            'panUp'		  :		$('#panup'),
            'panDown'		:		$('#pandown'),
            'panLeft'		:		$('#panleft'),
            'panRight'	:		$('#panright'),
            'fit'       :   $('#fit'),
            'destroy'   :   $('#destroy'),
            'out_x1'    :   $('#x1'),
            'out_y1'    :   $('#y1'),
            'out_x2'    :   $('#x2'),
            'out_y2'    :   $('#y2'),
            'directedit':   true,
            'debug'     :   false
          });
        };

        initPanZoom();