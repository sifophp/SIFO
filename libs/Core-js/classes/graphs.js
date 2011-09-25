Core.classes.graph = {
	oDefault : {
		from: 'data',
		width: null,
		height: null,
		xaxisMode : 'time',
		dateFormat: 'EU',
		tooltip: {
			id: 'tooltip',
			position: 'absolute',
			display: 'none',
			border: '1px solid #CCC',
			padding: '5px',
			backgroundColor: '#FFF',
			opacity: 1
			}
	}
};

Core.classes.graph.createTarget = function (type,element,target,width,height,position)
{
	var t = document.createElement(type);
	t.setAttribute('id', element);
	t.setAttribute('width', width);
	t.setAttribute('height', height);
	if (position === 'after') {
		$(document.getElementById(target)).after(t);
	} else {
		$(document.getElementById(target)).before(t);
	}
};
    
Core.classes.graph.showTooltip = function (x, y, contents, oSettings) {
	$('<div id="'+ oSettings.tooltip.id + '">' + contents + '</div>').css( {
		position: oSettings.tooltip.position,
		display: oSettings.tooltip.display,
		top: y + 10,
		left: x + 8,
		border: oSettings.tooltip.border,
		padding: oSettings.tooltip.padding,
		'background-color': oSettings.tooltip.backgroundColor,
		opacity: oSettings.tooltip.opacity
	}).appendTo("body").fadeIn(200);
};

Core.classes.graph.showLocalDate = function (timestamp)
{

	var nDate = Math.round(timestamp);
	var dt = new Date( nDate );
	var mm = dt.getMonth() + 1;
	
	return dt.getDate() + "/" + mm + "/" + dt.getFullYear();
};

Core.classes.graph.init = function (oOptions){

	var self = this;
	var Cm = Core.modules;
	var Cc = Core.classes;
	var Cg = Core.globals;
	var oSettings = self.oDefault;
	var key = '';

	for ( key in oOptions )	{
            if(oOptions.hasOwnProperty(key)) {
                oSettings[key] = oOptions[key];
            }
	}

	var dateFormat = '';
        if (oSettings.dateFormat === "US")
        {
                dateFormat = "%m/%d";
        } else {
                dateFormat = "%d/%m";
        }

        $(document.getElementById(oSettings.from)).graphTable(
                {
                        width: oSettings.width,
						height: oSettings.height,
						series: 'columns',
                        xaxisTransform: function(s){
                                var date = s;
                                if (oSettings.dateFormat !== 'US')
                                {
                                        var current_date = s.split("/");
                                        date = Date.parse(current_date[1]+"/"+current_date[0]+"/"+current_date[2]);
                                }
                                return(date);
                        }
                },
                {
                        series: {
           lines: { show: true },
           points: { show: true }
                        },
                        grid: {
                                hoverable: true,
                                clickable: true
                        },
                        legend: {
                                position: 'sw'
                        },
                        xaxis: {
                                mode: oSettings.xaxisMode,
                                timeformat: dateFormat,
                                ticks:6,
                                minTickSize :[1,'day']
                        }
                }
        );

        var previousPoint = null;
        $(".flot-graph").bind("plothover", function (event, pos, item) {
                $("#x").text(pos.x.toFixed(2));
                $("#y").text(pos.y.toFixed(2));


                        if (item) {
                                if (previousPoint !== item.datapoint) {
                                        previousPoint = item.datapoint;

                                        $("#tooltip").remove();

                                        
										
										var x = self.showLocalDate(item.datapoint[0].toFixed(2));
                                        var y = Math.round(item.datapoint[1].toFixed(2));

                                        self.showTooltip(item.pageX, item.pageY,  "<strong>" + y +" "+ item.series.label + "</strong><br/>("+ x +")" , oSettings);
                                }
                        }
                        else {
                                $("#tooltip").remove();
                                previousPoint = null;
                        }
        });

	if (oSettings.hide_from !== false) {
            $(document.getElementById(oSettings.from)).hide();
        }

};