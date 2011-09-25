Core.classes.sortable = {
	oDefault : {
		target: '.sortable',
		pathCss : '',
		handle : 'div.sort_handler',
		opacity: 0.5,
		revert:	true,
		update: function(){}
	}
};

Core.classes.sortable.init = function (oOptions){

	var Cm = Core.modules; 
	var Cc = Core.classes;
	var Cg = Core.globals;

	var oSettings = this.oDefault;
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

	Core.classes.sortable.load(oSettings);
	
};

Core.classes.sortable.load = function (oSettings){
	
		$(oSettings.target).sortable();
		$(oSettings.target).disableSelection();	
};

