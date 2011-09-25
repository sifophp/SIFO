Core.classes.editable = {
    oDefault : {
		target	  : '.editable',
		url	  : function(value,settings) { return(value); },
		event	  : 'click',
		type      : 'textarea',
		cancel    : 'Cancel',
		submit    : 'Save',
		indicator : 'Saving...',
		tooltip   : 'Click to edit...',
		cssclass : 'editable',
		callback : function(value, settings) {
		    var $this = $(this);
		    var sColor = $this.css('background-color');
	
		    $this
			.animate( { backgroundColor: "#d5fbc1" }, 1)
			.animate( { backgroundColor: sColor }, 2000);
		},
		rows: 5,
		cols: 200,
		height: 'auto',
		width: 'auto',
		submitdata : function() { return false;	},
		pathCss : ''
    }
};


Core.classes.editable.init = function (oOptions){

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

    // Load the CSS
    if (oSettings.pathCss) {
		$(document.body).append('<link rel="stylesheet" type="text/css" href="'+ oSettings.pathCss +'" />');
    }

    Core.classes.editable.load(oSettings);

};

Core.classes.editable.load = function (oSettings){

    $(oSettings.target).editable(oSettings.url, {
	type      : oSettings.type,
	cancel    : oSettings.cancel,
	submit    : oSettings.submit,
	indicator : oSettings.indicator,
	tooltip   : oSettings.tooltip,
	event   : oSettings.event,
	cssclass : oSettings.cssclass,
	callback : oSettings.callback,
	rows: oSettings.rows,
	cols:  oSettings.cols,
	height: oSettings.height,
	width: oSettings.width
    });

};
