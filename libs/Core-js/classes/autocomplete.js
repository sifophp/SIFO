CORE.classes.autocomplete = {
	oDefault : {
		targetId: 'autocomplete',
		data : [],
		autoOpen: false,
		autoOpenDelay: 100,
		autoFill: false,
		minChars: 0,
		max: 12,
		mustMatch: false,
		matchContains: false,
		scrollHeight: 220,
		pathCss : ''
	}
};

CORE.classes.autocomplete.init = function (oOptions){

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
	if (oSettings.pathCss)	{
		$(document.body).append('<link rel="stylesheet" type="text/css" href="'+ oSettings.pathCss +'" />');
	}

	Cc.autocomplete.load(oSettings);
	
};

CORE.classes.autocomplete.load = function (oSettings){

	var $Target = $(document.getElementById(oSettings.targetId));
	
	$Target.autocomplete(oSettings.data, oSettings);

	if (oSettings.autoOpen)
	{
		$Target.focus( function() {
			setTimeout ( function() {
				$Target.click();
			}, oSettings.autoOpenDelay );
		});
	}

};