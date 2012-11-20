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

$(document).ready(function() {
	var CP = CORE.behaviour.page,
		sId = '';

	// To Execute behaviours based on modular elements
	for ( var nCounter = 0; nCounter < CORE.modules.length; nCounter++  ) {
		sId = CORE.modules[nCounter];
		if ( document.getElementById( sId ) !== null )
		{
			CORE.behaviour.modules[ sId ].init();
			console.log( sId );
		}
	}

	// To execute behaviours based on pages
	if (typeof CP[document.body.id] != "undefined")
	{
		CP[document.body.id]();
	} else {
		CP.unset();
	}


});