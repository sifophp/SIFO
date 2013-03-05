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

	var sRel,
		aClasses;

	$(oTarget).each( function() {

		if ( this.className.indexOf('group') != -1 ) {
			aClasses = this.className.split(' ');

			sRel = '';

			for( var nCounter = 0; nCounter < aClasses.length; nCounter++) {
				if ( aClasses[nCounter].indexOf('group') != -1 ){
					sRel = aClasses[nCounter];
				}
			}
		}

		$(this).colorbox({rel: sRel});

	});

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
	$.colorbox.close()
};