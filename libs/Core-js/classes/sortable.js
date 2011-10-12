CORE.classes.sortable = {
	oDefault : {
		target: '.sortable',
		pathCss : '',
		handle : 'div.sort_handler',
		opacity: 0.5,
		revert:	true,
		update: function(){}
	}
};

CORE.classes.sortable.init = function (oOptions){

	var Cm = CORE.modules; 
	var Cc = CORE.classes;
	var Cg = CORE.globals;

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

	CORE.classes.sortable.load(oSettings);
	
};

CORE.classes.sortable.load = function (oSettings){
	
		$(oSettings.target).sortable();
		$(oSettings.target).disableSelection();	
};

