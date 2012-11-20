$LAB.setOptions(
{
	AlwaysPreserveOrder: false,
	UsePreloading: true,
	UseLocalXHR: true,
	UseCachePreload: true,
	AllowDuplicates: false,
	AppendTo: "head",
	BasePath: ""
});

CORE.behaviour.modules.isset = function( sId ) {

};


$(document).ready(function() {
	var CP = CORE.behaviour.page,
		sId = '';

	// To Execute behaviours based on modular elements
	for ( var nCounter = 0; nCounter < CORE.modules.length; nCounter++  ) {
		sId = CORE.modules[nCounter];
		if ( document.getElementById( sId ) !== null )
		{
			CORE.behaviour.modules[ sId ].init();
			if ( console ) {
				console.log( sId );
			}
		}
	}

    // Always execute the common behaviour
    CP['common']();

	// To execute behaviours based on pages
	if (typeof CP[document.body.id] != "undefined")
	{
        CP[document.body.id]();
	} else {
		CP.unset();
	}


});