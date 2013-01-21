CORE.classes.modal = {
    oDefault : {
		sUrl : '#' // Default URL to open
    }
};

CORE.classes.modal.init = function (oOptions){

    var self = this;
    var Cm = CORE.modules;
    var Cc = CORE.classes;
    var Cg = CORE.globals;
    var oSettings = self.oDefault;
    var key = '';

    for ( key in oOptions )	{
		if(oOptions.hasOwnProperty(key)) {
			oSettings[key] = oOptions[key];
		}
    }

    // Load the CSS
    if (oSettings.pathCss) {
		// Load the nyroModal Css
		//console.log (oSettings.pathCss);
		$(document.body).append('<link rel="stylesheet" type="text/css" href="'+ oSettings.pathCss +'" />');
    }

	Cc.modal.bind(oSettings);

};

CORE.classes.modal.autobind = function (oTarget){

	$(oTarget).colorbox();

};

CORE.classes.modal.open = function (oOptions){

	var self = this;
	var oSettings = self.oDefault;

	if (oOptions === undefined)
	{
		var oOptions = {};
	}

	for ( key in oOptions )	{
		if(oOptions.hasOwnProperty(key)) {
			oSettings[key] = oOptions[key];
		}
    }


	if(oSettings.sUrl !== undefined || oSettings.sUrl !== '#') {
		$.colorbox(oSettings);
	}

};

CORE.classes.modal.close = function(){
	//$.nmTop().close();
};